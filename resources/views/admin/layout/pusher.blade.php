<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('298433326050a121acc8', {
        cluster: 'mt1'
    });

    var channel = pusher.subscribe('new-chat-channel');
    channel.bind('App\\Events\\ChatEvent', function (data) {


        var pusher_product=data.product;
        var pusher_color=data.color;
        var pusher_size=data.size;
        var pusher_qty=data.qty;



        $(`.inventory_${pusher_product}_${pusher_color}_${pusher_size}`).text(pusher_qty)



    });

</script>