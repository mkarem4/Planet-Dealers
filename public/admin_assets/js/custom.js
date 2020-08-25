function change_lang(lang)
{
    $('#lang_input').val(lang);
    $('#lang_form').submit();
}

$('.btn-file').on('click', function()
{
    $(this).parent().find(".input-file").click();
});

$('.input-file').on('change', function(event)
{
    var name = event. target. files[0].name;
    $(this).parent().find(".btn-file").html(name);
});

$('.btn-files').on('click', function()
{
    $(this).parent().find(".input-files").click();
});

$('.input-files').on('change', function(event)
{
    var name = event.target.files.length;
    $(this).parent().find(".btn-files").html(name);
});

function modal_feature(id,featured,featured_till)
{
    $('#id_feature').val(id);

    if(featured)
    {
        $('#featured').prop('checked',true);
        $featured_till = $('#featured_till_input');
        $featured_till.val(featured_till);
        $featured_till.show();

        $('#featured_till').show();
    }
    else
    {
        $('#featured').prop('checked',false);
        $featured_till = $('#featured_till_input');
        $featured_till.val('');

        $featured_till.hide();
        $('#featured_till').hide();
    }
}


$('#featured').on('change',function()
{
    if($(this).prop('checked'))
    {
        $('#featured_till').show();
        $('#featured_till_input').show();
    }
    else
    {
        $('#featured_till').hide();
        $('#featured_till_input').hide();
    }
});


function modal_activate(id,type = null)
{
    if(type === 'main')
    {
        $('.main-text').show();
        $('.sub-text').hide();
    }
    else if(type === 'sub')
    {
        $('.sub-text').show();
        $('.main-text').hide();
    }

    $('#id_activate').val(id);
}


function modal_suspend(id,type = null)
{
    if(type === 'main')
    {
        $('.main-text').show();
        $('.sub-text').hide();
    }
    else if(type === 'sub')
    {
        $('.sub-text').show();
        $('.main-text').hide();
    }

    $('#id_suspend').val(id);
}


function modal_destroy(id,type = null)
{
    if(type === 'main')
    {
        $('.main-text').show();
        $('.sub-text').hide();
    }
    else if(type === 'sub')
    {
        $('.sub-text').show();
        $('.main-text').hide();
    }

    $('#id_destroy').val(id);
}


function modal_specialize(id)
{
    $('#id_special').val(id);
}


function modal_normalize(id)
{
    $('#id_normal').val(id);
}


$('.delete_check').on('click',function()
{
    $image =$(this).parent().find('.gallery-image');
    if($image.hasClass('removed'))
    {
        $image.css('opacity','1');
        $image.removeClass('removed');
    }
    else
    {
        $image.css('opacity','0.4');
        $image.addClass('removed');
    }
});


$('.check_all').on('change',function()
{
    if($(this).prop('checked')) $(this).parent().parent().parent().find(':checkbox').prop("checked", true);
    else $(this).parent().parent().parent().find(':checkbox').prop("checked", false);
});





