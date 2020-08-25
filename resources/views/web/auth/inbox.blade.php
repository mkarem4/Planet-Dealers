@extends('web.layout')
@section('content')
    <div tabindex="-1" class="site-content" id="content">
        <div class="container">

            <nav class="woocommerce-breadcrumb"><a href="/">{{word('home')}}</a><span class="delimiter"><i class="fa fa-angle-right"></i></span>{{word('messages')}}</nav>
            <div class="content-area" id="primary">
                <main class="site-main" id="main">
                    <article class="page type-page status-publish hentry">
                        <div itemprop="mainContentOfPage" class="entry-content">
                            <div id="yith-wcwl-messages"></div>

                            <!-- TITLE -->
                            <div class="wishlist-title ">
                                <h2>{{word('inbox')}}</h2>
                            </div>
                            <div id="frame">
                                <div id="sidepanel">
                                    <div id="profile">
                                        <div class="wrap">
                                            <img id="profile-img" src="{{user()->image}}" class="online" alt="" />
                                            <p>{{user()->name}}</p>
                                        </div>
                                    </div>
                                    <div id="contacts">
                                        <ul id="ul-messages">
                                            @foreach($users as $user)
                                                <li class="contact {{$loop->first ? 'active' : ''}} contact-{{$user->id}}" data-id="{{$user->id}}">
                                                <div class="wrap">
                                                    <img src="{{asset($user->image)}}" alt="" />
                                                    <div class="meta">
                                                        <p class="name">{{$user->name}} <label id="user_counter_{{$user->id}}" data-value="{{$user->not_seen}}">( {{$user->not_seen}} )</label></p>
                                                        <p class="preview">{{$user->text}}</p>
                                                        <p class="message-time-{{lang() == 'ar' ? 'ar' : 'en'}}-side">
                                                            {{$user->created_at->diffForHumans()}}
                                                        </p>
                                                        <br/>
                                                    </div>
                                                </div>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="content">
                                    <div class="contact-profile">
                                    </div>
                                    <div class="messages">
                                        <ul class="messages-list">
                                        </ul>
                                    </div>
                                    <div class="message-input">
                                        <div class="wrap">
                                            <input type="text" name="text" id="message" placeholder="{{word('type_ur_message')}}"/>
                                            <i class="fa fa-paperclip attachment" aria-hidden="true"></i>
                                            <button class="button" id="send_msg"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                                        </div>
                                        <input type="hidden" name="target_id" id="target_id">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </article>
                </main>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready( function ()
        {
            $('.contact').first().click();
            $('#target_id').val($('.contact').first().data('id'));
        });

        $('.contact'). click( function()
        {
            var target_id = $(this).attr('data-id');

            $('#target_id').val(target_id);
            $.ajax
            (
                {
                    url : '/profile/message/fetch',
                    method : 'post',
                    data : { target_id : target_id, _token: '{{csrf_token()}}'},
                    dataType : 'json',
                    success : function (data)
                    {
                        $('#target_id').val(target_id);
                        $('.messages-list').empty();

                        $.each(data.messages, function(i,msg)
                        {
                            if(msg.sender_id == {{user()->id}})
                            {
                                $('.messages-list').append('<li class="replies"><img src="'+ msg.sender.image +'" alt="" />\n' + '<p>'+ msg.text +'<span class="message-time-{{lang()}}">'+ msg.date_time +'</span></p></li>');
                            }
                            else
                            {
                                $('.messages-list').append('<li class="sent"><img src="'+ msg.sender.image +'" alt="" />\n' + '<p>'+ msg.text +'<span class="message-time-{{lang()}}">'+ msg.date_time +'</span></p></li>');
                            }
                        });

                        $('.contact-profile').html('');
                        $('.contact-profile').html('<img src="'+ data.user.image + '" alt="" />\n' +
                            '<p>'+ data.user.name +'</p>');

                        $('#user_counter_'+data.user.id).html('( 0 )');
                        $('.contact').removeClass('active');
                        $('#contact-' + target_id).parent().addClass('active');

                        var add_scroll = $(".messages-list li:last").outerHeight(true);

                        // $(".messages").animate({ scrollTop: $(".messages-list").scrollTop() + add_scroll});

                        $('#msg-counter').html(data.unread);
                    },
                    error : function()
                    {
                        console.log('fetch error');
                    }
                }
            );
        });


        $('#send_msg').click( function()
        {
            if($('#message').val() !== '')
                {
                    var target_id = $('#target_id').val();
                    var form_data = new FormData();

                    form_data.append('_token','{{csrf_token()}}');
                    form_data.append('target_id',target_id);
                    form_data.append('text',$('#message').val());

                    $('.messages-list').append('<li class="replies" id="latest_msg"><img src="{{user()->image}}" alt="" />\n' + '<p>'+ $('#message').val() +'<span class="message-time-{{lang()}}">{{\Carbon\Carbon::now()->toDateTimeString()}}</span></p></li>');
                    $('#message').val('');

                    var add_scroll = $(".messages-list li:last").outerHeight(true);

                    $(".messages").animate({ scrollTop: $(".messages-list").scrollTop() + add_scroll});

                    $.ajax
                    (
                        {
                            url : '/profile/message/store',
                            method : 'post',
                            data : form_data,
                            dataType : 'json',
                            processData: false,
                            contentType: false,
                            success :  function(data)
                            {
                                if(data.status != 'success')
                                {
                                    $('#latest_msg').remove();

                                    var add_scroll = $(".messages-list li:last").outerHeight(true);
                                    // $(".messages-list").animate({ scrollTop: $(".messages-list").scrollTop() + add_scroll});

                                    console.log('send error',data.status);
                                }
                            },
                            error : function(data,)
                            {
                                console.log('send error',data);
                            }
                        }
                    )
                }
        });

    </script>
@endsection
