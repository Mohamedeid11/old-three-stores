<?php

namespace App\Traits;
use App\SellOrder;


trait JT
{
    /**
     * 内容部分加密
     * @return string
     */
    function get_content_digest($customerCode,$pwd,$key)
    {
        $str = strtoupper($customerCode . md5($pwd . 'jadada236t2')) . $key;
        return base64_encode(pack('H*', strtoupper(md5($str))));
    }
    /**
     * 头部请求部分加密
     * param array $post
     * @return string
     */
    function get_header_digest($post,$key){
        $digest = base64_encode(pack('H*',strtoupper(md5($post.$key))));
        return $digest;
    }


    function get_post_data($customerCode,$pwd,$key,$waybillinfo){

        $postdate = json_decode($waybillinfo,true);
        $postdate['customerCode'] = $customerCode;
        $postdate['digest'] = $this->get_content_digest($customerCode,$pwd,$key);
        return json_encode($postdate);
    }


    function create_order($customerCode,$pwd,$key,$account,$waybillinfo,$url) {

        $post_data = $this->get_post_data($customerCode,$pwd,$key,$waybillinfo);

        //print_r($post_data);
        $head_dagest = $this->get_header_digest($post_data,$key);
        //print_r($head_dagest);
        $post_content = array(
            'bizContent' => $post_data
        );
        $postdata = http_build_query($post_content);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' =>
                    array('Content-type: application/x-www-form-urlencoded',
                        'apiAccount:' . $account,
                        'digest:' . $head_dagest,
                        'timestamp: '.strtotime('now')
                    ),
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }


    public function init_order(SellOrder $order) {
        $key = "a0a1047cce70493c9d5d29704f05d0d9";
        $customerCode= "J0086024297";
        $pwd  = "Jt123456";
        $apiAccount = "292508153084379141";
        $openUrl = "https://demoopenapi.jtjms-sa.com/webopenplatformapi/api";
        if($order->note != '') {$note = $order->note;} else {$note = "Deliver Carefully";}

        $items = 0;
        $pieces = "";
        $total_deliver = 0;
        foreach ($order->itemsq as $item)
        {
            if($item->qty > 0)
            {
                $items = $items + $item->qty;
                $has_deliver = true;
                $total_deliver = $total_deliver + ($item->qty * $item->price);
            }
            else
            {
                $items = $items + (-1 * $item->qty);
                $has_return = true;
                $total_deliver = $total_deliver + ($item->qty * $item->price);
            }

            $pieces .= '{
                "itemName":"'.optional($item->product_info)->title.'",
            }';
        }

        // dd($order->id);
        // $waybillinfo = '{
        //     "serviceType":"02",
        //     "orderType":"2",
        //     "deliveryType":"04",
        //     "countryCode":"EGY",
        //     "receiver":{
        //         "address":"'.$order->address.'",
        //         "street":"",
        //         "city":"'.optional($order->city_info)->title.'",
        //         "mobile":"'.optional($order->client_info)->phone.'",
        //         "mailBox":"",
        //         "phone":"'.optional($order->client_info)->phone.'",
        //         "countryCode":"EGY",
        //         "name":"'.optional($order->client_info)->name.'",
        //         "company":"",
        //         "postCode":"",
        //         "prov":"'.optional($order->city_info)->title.'"
        //     },
        //     "expressType":"EZ",
        //     "length":0,
        //     "weight":15,
        //     "remark":"'.$note.'",
        //     "txlogisticId":"'.$order->order_number.'",
        //     "goodsType":"ITN7",
        //     "priceCurrency":"EGP",
        //     "totalQuantity":'.$items.',
        //     "sender":{
        //         "address":"Salasa WH Sulyffff",
        //         "street":"",
        //         "city":"Cairo",
        //         "mobile":"96650000000fff0",
        //         "mailBox":"three@gmail.com",
        //         "phone":"",
        //         "countryCode":"EGY",
        //         "name":"Salasa Test",
        //         "company":"Three Stores",
        //         "postCode":"",
        //         "prov":"Cairo"
        //     },
        //     "itemsValue":'.$total_deliver.',
        //     "offerFee":0,
        //     "items":['.$pieces .'],
        //     "operateType":1,
        //     "payType":"PP_CASH",
        //     "isUnpackEnabled":0
        // }';

        $waybillinfo = '{
            "serviceType":"02",
            "orderType":"2",
            "deliveryType":"04",
            "countryCode":"KSA",
            "receiver":{
                "address":"Riyadh, 20 sts ",
                "street":"",
                "city":"Riyadh",
                "mobile":"0533666345",
                "mailBox":"customer@gmail.com",
                "phone":"",
                "countryCode":"KSA",
                "name":"Omar Test",
                "company":"company",
                "postCode":"000001",
                "prov":"Riyadh"
            },
            "expressType":"EZKSA",
            "length":0,
            "weight":15,
            "remark":"description goes here",
            "txlogisticId":"tttest__2-2191982-2",
            "goodsType":"ITN1",
            "priceCurrency":"SAR",
            "totalQuantity":1,
            "sender":{
                "address":"Salasa WH Sulyffff",
                "street":"",
                "city":"Riyadh",
                "mobile":"96650000000fff0",
                "mailBox":"salasa@gmail.com",
                "phone":"",
                "countryCode":"KSA",
                "name":"Salasa Test",
                "company":"company",
                "postCode":"",
                "prov":"Riyadh"
            },
            "itemsValue":10,
            "offerFee":0,
            "items":[
                {
                    "englishName":"file",
                    "number":1,
                    "itemType":"ITN1",
                    "itemName":"\u6587\u4ef6\u7c7b\u578b",
                    "priceCurrency":"SAR",
                    "itemValue":"2000",
                    "itemUrl":"http:\/\/www.baidu.com",
                    "desc":"file"
                }
            ],
            "operateType":1,
            "payType":"PP_PM",
            "isUnpackEnabled":0
        }';
        $result = $this->create_order($customerCode,$pwd,$key,$apiAccount,$waybillinfo,$openUrl.'/order/addOrder');
        $data = json_decode($result);
        if ($data->code == 1) {
            
        }
        dd($data);
    }
}
