<?php
namespace MineCloudvod\Dogecloud;

class Oss{
    private $mediaTypes = array(
        "jpg"=> "image/jpeg",
        "jpeg"=> "image/jpeg",
        "jpe"=> "image/jpeg",
        "gif"=> "image/gif",
        "png"=> "image/png",
        "bmp"=> "image/bmp",
        "tiff"=> "image/tiff",
        "tif"=> "image/tiff",
        "ico"=> "image/x-icon",
        "vtt"=> "text/vtt",
    );
    private $upload;
    public function __construct(){
        add_action( 'mcv_add_admin_options_before_purchase', array( $this, 'doge_admin_options' ) );

        if(MINECLOUDVOD_SETTINGS['doge_oss']['sync_media'] ?? false){
            add_filter( 'wp_handle_upload', [ $this, 'wpHandleUpload' ] );
            
	        add_filter( 'wp_generate_attachment_metadata', [ $this, 'wpGenerateAttachmentMetadata' ], 10, 2 );
            add_filter( 'wp_get_attachment_url', [ $this, 'doge_media_url' ], 10, 2 );
            add_filter( 'attachment_url_to_postid', [ $this, 'url_to_postid' ], 10, 2 );

            add_action('delete_attachment', [ $this, 'deleteAttachment' ] );
            
            //rename
            // if( MINECLOUDVOD_SETTINGS['doge_oss']['rename'] ){
            //     add_filter( 'sanitize_file_name', [ $this, 'renameDogeFile' ] );
            // }
        }
        
    }

    /**
     * Rename doge file
     */
    // public function renameDogeFile( $filename ){
    //     $ext = '.' . pathinfo( $filename, PATHINFO_EXTENSION );
    //     $new_filename = rtrim( $filename, $ext) . '_' . mt_rand(1,999) . $ext;
    //     return $new_filename;
    // }

    /**
     * attachment_url_to_postids
     */
    public function url_to_postid( $post_id, $url ){
        if( !$post_id && isset( MINECLOUDVOD_SETTINGS['doge_oss']['domain'] ) && strpos( $url, MINECLOUDVOD_SETTINGS['doge_oss']['domain'] ) >= 0 ){
       
            global $wpdb;
            $dir  = wp_get_upload_dir();
            $path = str_replace( '//' . MINECLOUDVOD_SETTINGS['doge_oss']['domain'] . '/' . str_replace( ABSPATH, '', $dir['basedir'] ) . '/', '', $url );
            $sql = $wpdb->prepare(
                "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = '_wp_attached_file' AND meta_value = %s",
                $path
            );
            $results = $wpdb->get_results( $sql );
            $post_id = null;

            if ( $results ) {
                // Use the first available result, but prefer a case-sensitive match, if exists.
                $post_id = reset( $results )->post_id;

                if ( count( $results ) > 1 ) {
                    foreach ( $results as $result ) {
                        if ( $path === $result->meta_value ) {
                            $post_id = $result->post_id;
                            break;
                        }
                    }
                }
            }
            return (int)$post_id;
        }
    }
    
    public function deleteAttachment($post_id){
        $_is_mcv_doge = get_post_meta( $post_id, '_is_mcv_doge', true );
        if( $_is_mcv_doge ){
            $meta = wp_get_attachment_metadata( $post_id );
            $dir  = wp_get_upload_dir();
            $file_path = str_replace( ABSPATH, '', $dir['basedir'] . '/'. $meta['file'] );
            $file_dir = dirname( $file_path );
            $original_file_path = $file_dir . '/'. $meta['original_image'];
            $file_data = [
                $file_path,
                $original_file_path
            ];
            if (!empty($meta['sizes'])) {
                foreach ($meta['sizes'] as $size) {
                    $file_data[] = $file_dir . '/' . $size['file'];
                }
            }
            $this->del_doge_file( $file_data );
        }
    }
    public function del_doge_file( $file_data ){
        $data = [
            'bucket' => MINECLOUDVOD_SETTINGS['doge_oss']['bucket'],
        ];
        $resultArray = \MineCloudvod\RestApi\Dogecloud::call('/oss/file/delete.json?'.http_build_query($data), json_encode( $file_data ) );
    }

    public function doge_media_url($url, $post_id){
        $_is_mcv_doge = get_post_meta( $post_id, '_is_mcv_doge', true );
        if( $_is_mcv_doge ){
            $url = '//' . MINECLOUDVOD_SETTINGS['doge_oss']['domain'] . '/' . str_replace( ABSPATH, '', get_attached_file( $post_id ) );
        }
        return $url;
    }

    public function wpGenerateAttachmentMetadata( $metadata, $attachment_id  ){
        $mime_type = get_post_mime_type( $attachment_id );
        // if ( in_array( $mime_type, $this->mediaTypes ) ) {
            $upload_dir = wp_upload_dir();
            $file_path = $upload_dir['path'];
            $nfile = explode( '/', $metadata['file'] );
            $nfile = array_pop( $nfile );
            // 上传优化过的文件
            if( $nfile != $metadata['original_image'] ){
                $path = $file_path . '/' . $nfile;
                $upload_scaled = [
                    'type' => $mime_type,
                    'file' => $path,
                ];
                $this->upload_to_doge( $upload_scaled );
                $this->del_local_file( $upload_scaled );
            }
            // 上传所有尺寸的图片
            foreach ($metadata['sizes'] as $size) {
                $path = $file_path . '/' . $size['file'];
                $upload_size = [
                    'type' => $size['mime-type'],
                    'file' => $path,
                ];
                $this->upload_to_doge( $upload_size );
                $this->del_local_file( $upload_size );
            }
            update_post_meta( $attachment_id, '_is_mcv_doge', true );
            $this->del_local_file( $upload );
        // }
        return $metadata;
    }

    public function wpHandleUpload($upload){
        // if (in_array($upload['type'], $this->mediaTypes)) {
            $this->upload_to_doge( $upload );
        // }
        return $upload;
    }
    
    public function del_local_file( $upload ){
        if( MINECLOUDVOD_SETTINGS['doge_oss']['del_local'] ?? false ){
            @unlink($this->upload['file']);
            $this->upload = null;
        }
    }
    
    public function upload_to_doge( $upload ){
        $data = [
            'bucket' => MINECLOUDVOD_SETTINGS['doge_oss']['bucket'],
            'key' => str_replace(ABSPATH, '', $upload['file']) ,
        ];
        $file_data = file_get_contents($upload['file']);
        $resultArray = \MineCloudvod\RestApi\Dogecloud::call('/oss/upload/put.json?'.http_build_query($data), $file_data);

        if(isset($resultArray['code']) && $resultArray['code'] == 200){
            $this->upload = $upload;
        }
    }

    public function doge_admin_options(){
        $prefix = 'mcv_settings';

        \MCSF::createSection($prefix, array(
            'parent'     => 'mcv_doge',
            'title'  => __('Doge OSS', 'mine-cloudvod'),
            'icon'   => 'far fa-file',
            'fields' => array(
                array(
                    'type'    => 'submessage',
                    'style'   => 'warning',
                    'content' => __('<a href="https://www.dogecloud.com/?iuid=2453" target="_blank">Dogecloud官网</a> ', 'mine-cloudvod'), 
                ),
                array(
                    'id'        => 'doge_oss',
                    'type'      => 'fieldset',
                    'title'     => '',
                    'fields'    => array(
                        array(
                            'id'    => 'sync_media',
                            'type'  => 'switcher',
                            'title' => __('Sync Media to OSS', 'mine-cloudvod'),
                            'text_on'    => __('Enable', 'mine-cloudvod'),
                            'text_off'   => __('Disable', 'mine-cloudvod'),
                            'before' => '<p>注意：只支持 10MB 以内的小文件同步</p><p>启用后，新上传的媒体文件会自动同步到<a href="https://www.dogecloud.com/?iuid=2453" target="_blank">Dogecloud</a>云存储的指定空间内。</p>',
                            'default' => false,
                        ),
                        array(
                            'id'    => 'bucket',
                            'type'  => 'text',
                            'title' => __('Bucket' , 'mine-cloudvod'),
                            'dependency' => array('sync_media', '==', true),
                        ),
                        array(
                            'id'    => 'domain',
                            'type'  => 'text',
                            'title' => __('Domain' , 'mine-cloudvod'),
                            'dependency' => array('sync_media', '==', true),
                        ),
                        array(
                            'id'    => 'del_local',
                            'type'  => 'switcher',
                            'title' => __('Delete Local Media', 'mine-cloudvod'),
                            'text_on'    => __('Enable', 'mine-cloudvod'),
                            'text_off'   => __('Disable', 'mine-cloudvod'),
                            'after' => '同步成功后，是否删除本地文件。',
                            'dependency' => array('sync_media', '==', true),
                            'default' => false,
                        ),
                        // array(
                        //     'id'    => 'rename',
                        //     'type'  => 'switcher',
                        //     'title' => '重命名文件',
                        //     'text_on'    => __('Enable', 'mine-cloudvod'),
                        //     'text_off'   => __('Disable', 'mine-cloudvod'),
                        //     'after' => '可有效防止文件名重复而被覆盖。',
                        //     'dependency' => array('sync_media', '==', true),
                        //     'default' => false,
                        // ),
                    ),
                ),
            )
        ));
    }
}
