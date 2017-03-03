<?php 
require_once('lib/Vimeo.php');

$consumer_key = "7d56781df87ee448ef321ab19d7e8e93cabf8533";
$consumer_secret_key = "TeYM3/6CBPLaVYPgroBuP7JGeuPox+DnRjqV5P9nD4pV8hbKhesE0xtrXhaKI17GIpfzIM9GRDUMysJI8jlor1utVIqdUYL/lvF/Lo7sIPoFAT/lgiUh0cvFYQ3ePfRW";
$access_token = "ccac204f159c17ef2eb49903e6f5353a";

$vimeo = new Vimeo($consumer_key, $consumer_secret_key, $access_token);

$channel_video_array = [];

$video_data_array =  get_channel_video_data();

echo count($video_data_array);

function get_channel_video_data($page_number = null) {

    $video_endpoint_url = '/channels/nicetype/videos?per_page=100';

    if($page_number != null)
        $video_endpoint_url.'&page='.$page_number;
    
    $channel_data = $GLOBALS['vimeo']->request($video_endpoint_url);
    $rate_ratelimit_remaining = $channel_data['headers']['X-RateLimit-Remaining'];

    if($rate_ratelimit_remaining == 0):
        echo "Rate limit exceeded. Please try again some time";
        return false;
    else:
        $paging_object = (object) $channel_data['body']['paging'];
        $video_array = $channel_data['body']['data'];
        foreach($video_array as $video) {
            array_push($channel_video_array, $video);
        }
        $next_page = getPageNumber($paging_object->next,'page');

        if($next_page == null) {
            return $channel_video_array;
        } else {
            get_channel_video_data($next_page);
        }
    endif;

}

function getPageNumber($url, $url_param) {
    $parsed_url = parse_url($url);
    parse_str($parsed_url['query'], $query);
    return $query[$url_param];
}

?>