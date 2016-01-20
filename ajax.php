<?php
$error = FALSE;
if(file_exists('config.php')){
    require_once('lib/yelp.php'); 
    $message = '';
    if(!isset($CONSUMER_KEY) || empty($CONSUMER_KEY)){
        $message .= ' $CONSUMER_KEY is empty.';
        $error = TRUE;
    }
    
    if(!isset($CONSUMER_SECRET) || empty($CONSUMER_SECRET)){
        $message .= ' $CONSUMER_SECRET is empty.';
        $error = TRUE;
    }
    
    if(!isset($TOKEN) || empty($TOKEN)){
        $message .= ' $TOKEN is empty.';
        $error = TRUE;
    }
    
    if(!isset($TOKEN_SECRET) || empty($TOKEN_SECRET)){
        $message .= ' $TOKEN_SECRET is empty.';
        $error = TRUE;
    }
    if($error == TRUE){
        $message .= ' Please edit <strong>config.php</strong>, keys can be obtained from <a href="http://www.yelp.com/developers">http://www.yelp.com/developers</a>';
    }
    
} else {
    $error = TRUE;
    $message = 'Missing <strong>config.php</strong>, Please copy and rename the file <strong>config.php.sample</strong> to <strong>config.php</strong> and provide valid API Keys. API keys can be obtained from'.
            ' <a href="http://www.yelp.com/developers">http://www.yelp.com/developers</a>';
    
}
if($error == TRUE){
    $data = array();
    $results['recordsTotal'] = 0;
    $results['recordsFiltered'] = 0;
    $results['dataError'] = array('message'=>$message);
    $results['draw'] = (int) $draw;
    $results['data'] = $data;
    echo json_encode($results);
    die();
}

/* Magic Begins here! */

extract($_POST);

$data = array();
$results['recordsTotal'] = 0;
$results['recordsFiltered'] = 0;

if(isset($term) && isset($location) && isset($length) && isset($start)){
    $response = json_decode(search($term,$location,$length,$start));
    
    $businesses = array();
    
    if($response->businesses){
        $businesses = $response->businesses;
    }
    
    foreach($businesses as $business){
        if(isset($business->name)){
            $name = $business->name;
        } else {
            $name = '';
        }
        
        if(isset($business->display_phone)){
            $phone_display = $business->display_phone;
        } else {
            $phone_display = '';
        }

        if(isset($business->phone)){
            $phone = $business->phone;
        } else {
            $phone = '';
        }

        if(isset($business->location->display_address)){
            $display_address = $business->location->display_address;
        } else {
            $display_address = '';
        }
        
        if(isset($business->location->address)){
            $address = $business->location->address;
        } else {
            $address = '';
        }
        
        if(isset($business->location->neighborhoods)){
            $neighborhoods = $business->location->neighborhoods;
        } else {
            $neighborhoods = '';
        }
        
        if(isset($business->location->city)){
            $city = $business->location->city;
        } else {
            $city = '';
        }
        
        if(isset($business->location->state_code)){
            $state = $business->location->state_code;
        } else {
            $state = '';
        }
        
        if(isset($business->location->postal_code)){
            $postal_code = $business->location->postal_code;
        } else {
            $postal_code = '';
        }
        
        if(isset($business->url)){
            $url = $business->url;
        } else {
            $url = '';
        }
        
        $categories = array();

        if(isset($business->categories)){
            foreach($business->categories as $bizCategory){
                if(isset($bizCategory[0])){
                    $categories[] = $bizCategory[0];
                }
            }
        }
        
        $category_1 = '';
        $category_2 = '';
        $category_3 = '';
        $category_others = '';
        
        if(isset($business->categories[0][0])){
            $category_1 = $business->categories[0][0];
        }
        
        if(isset($business->categories[1][0])){
            $category_2 = $business->categories[1][0];
        }
        
        if(isset($business->categories[2][0])){
            $category_3 = $business->categories[2][0];
        }
        
        if(isset($business->categories[3])){
            $category_others = implode(', ',array_slice($categories,3));
        }
        
        $categories = implode(', ', $categories);
        
//        if(isset($neighborhoods[0])){ // removed on request
//            $address[] = $neighborhoods[0]; //add first neighbourhood to address for display
//        }
        
        if(isset($business->rating)){
            $rating = $business->rating;
        } else {
            $rating = '';
        }
        
        if(isset($business->rating_img_url)){
            $rating_img_url = $business->rating_img_url;
        } else {
            $rating_img_url = '';
        }
        
        if($rating_img_url != ''){
            $rating_img = '<img src="'.$rating_img_url.'">';
        } else {
            $rating_img = '';
        }
        
        $display_url = '';
        $display_link = '';
        
        if($url){
            if(strlen($url) > 25){
                $display_url = substr($url, 0, 25);
                $display_link = '<a href="'.$url.'" target="_blank" data-toggle="tooltip" data-placement="bottom" title="'.$url.'">'.$display_url.'...</a>';
            } else {
                $display_url = $url;
                $display_link = '<a href="'.$url.'" target="_blank" data-toggle="tooltip" data-placement="bottom" title="'.$url.'">'.$display_url.'</a>';
            }
        }
        
        $data[] = array(
            'name' => $name,
            'phone_display' => $phone_display,
            'phone' => $phone,
            'address' => implode(', ',$address),
            'city' => $city,
            'state' => $state,
            'zip'   => $postal_code,
            //'category' => $categories,
            'category_1' => $category_1,
            'category_2' => $category_2,
            'category_3' => $category_3,
            'category_others' => $category_others,
            'url_link'   => $display_link,
            'url' => $url,
            'rating_img' => $rating_img,
            'rating'=> $rating //for csv
            );
    }
    
    if(is_numeric($response->total)){
        $results['recordsTotal'] = $response->total;
        $results['recordsFiltered'] = $response->total;
    }   
} else {
    $results['dataError'] = array('message'=>'Missing parameters');
}

//$results['test'] = $business;
$results['draw'] = (int) $draw;
$results['data'] = $data;
echo json_encode($results);
die();
