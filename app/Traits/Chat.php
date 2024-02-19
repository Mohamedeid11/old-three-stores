<?php


namespace App\Traits;


use Pusher\Pusher;

trait Chat
{

   static public function notify($qty,$product,$color,$size)
    {
        $options = array(
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'encrypted' => true
        );
        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $data['qty'] = $qty;
        $data['product'] = $product;
        $data['color'] = $color;
        $data['size'] = $size;



        $pusher->trigger('new-chat-channel', 'App\\Events\\ChatEvent', $data);

    }
}