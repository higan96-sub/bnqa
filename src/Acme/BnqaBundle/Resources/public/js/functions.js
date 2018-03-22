/**
 * Created with JetBrains PhpStorm.
 * User: nrhk
 * Date: 2013/08/06
 * Time: 20:41
 * To change this template use File | Settings | File Templates.
 */
function followOrRemove() {
    $('.js-action-follow-item').click(function () {
        var asinCode = $(this).parents('.item-unit').attr('id');
        //remove
        if ($(this).hasClass('following')) {
            $.ajax({
                type: "POST",
                url: "/following/remove/item",
                data: {
                    asinCode: asinCode
                },
                dataType: 'json',
                context: this,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf_token);
                }
            }).done(function (data, status, xhr) {
                    $(this).removeClass('following');
                    $(this).text('フォローする');
                    if($(this).hasClass('btn')){
                        $(this).removeClass('btn-danger');
                        $(this).addClass('btn-default');
                    }


                })
                .fail(function (data) {
                })
                .always(function (data) {
                    $(this).removeClass('following');
                    $(this).text('フォローする');
                    if($(this).hasClass('btn')){
                        $(this).removeClass('btn-danger');
                        $(this).addClass('btn-default');
                    }
                });
        } else {
            if ($(this).hasClass('btn-info') == false) {
                //create
                $(this).text('Loading...');
                $.ajax({
                    type: "POST",
                    url: "/following/create/item",
                    data: {
                        asinCode: asinCode
                    },
                    dataType: 'json',
                    context: this,
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf_token);
                    }
                }).done(function (data, status, xhr) {
                        $(this).addClass('following');
                        if($(this).hasClass('btn')){
                            $(this).addClass('btn-info');
                        }
                        $(this).text('フォロー中');
                    })
                    .fail(function (data) {
                        $(this).removeClass('btn-info')
                        $(this).text('フォローする');
                    })
                    .always(function (data) {
                        $(this).addClass('following');
                        if($(this).hasClass('btn')){
                            $(this).addClass('btn-info');
                        }
                        $(this).text('フォロー中');
                    }
                );
            }
        }
        followButton();
    });
}
function editorReply(){

    $('.js-action-reply').click(function () {
        $('#report_replyTo').val($(this).parents('.report-unit').attr('id'));
        $('#reportBook_replyTo').val($(this).parents('.report-unit').attr('id'));
        $('#report_asinCode').val('false');
    });
}
function followButton() {
    $('.js-action-follow-item.btn-info').mouseover(function () {
        if ($(this).hasClass('btn-info')) {
            $(this).removeClass('btn-info');
            $(this).addClass('btn-danger');
            $(this).text('解除');
        }
    });
    $('.js-action-follow-item.btn-info').mouseout(function () {
        if ($(this).hasClass('btn-danger')) {
            $(this).removeClass('btn-danger');
            $(this).addClass('btn-info');
            $(this).text('フォロー中');
        }
    });
}

function editorItem() {
    $('.edit-button').click(function () {
        $('#report_replyTo').val('false');
        $('#reportBook_replyTo').val('false');
        $('#report_asinCode').val($(this).parents('.item-unit').attr('id'));
        $('#reportBook_asinCode').val($(this).parents('.item-unit').attr('id'));
    });
}

function bookmark() {
    $('a.js-action-bookmark').click(function () {
        var report = $(this).parents(".report-unit").attr("id");
        if ($(this).hasClass('bookmarked')) {
            $.ajax({
                type: "POST",
                url: "/reportbookmark/delete",
                data: {
                    report: report
                },
                dataType: 'json',
                context: this,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf_token);
                }
            }).done(function (data, status, xhr) {
                    $(this).html('<i class="icon-bookmark"></i>ブックマークする');
                    $(this).removeClass('bookmarked')
                }).fail(function (data, status, xhr) {
                }).always(function (data, status, xhr) {
                    $(this).html('<i class="icon-bookmark"></i>ブックマークする');
                    $(this).removeClass('bookmarked')
                }
            );
        }
        else {
            $.ajax({
                type: "POST",
                url: "/reportbookmark/create",
                data: {
                    report: report
                },
                dataType: 'json',
                context: this,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf_token);
                }
            }).done(function (data, status, xhr) {
                    $(this).html('<i class="icon-bookmark"></i>ブックマーク済み');
                    $(this).addClass('bookmarked');
                }).fail(function (data, status, xhr) {
                })
                .always(function (data, status, xhr) {
                    $(this).html('<i class="icon-bookmark"></i>ブックマーク済み');
                    $(this).addClass('bookmarked');
                }
            );
        }
        return false;
    });
}

function editorDelete(){
    $('.js-action-del').click(function () {
            var img = $(this).parents().find(".report-img-unit").html();
            var body = $(this).parents().find(".report-body-unit").html();
            var title = $(this).parents().find(".report-title-unit").html();
//            var body = $(this).parents(".unit").find(".report-body").html();
            $('.del-modal-img').html(img);
            $('.del-modal-body').html(body);
            $('.del-modal-title').html(title);
            reportId = ($(this).parents(".report-unit").attr('id'));
//            $('#form_item_id').val($(".item-detail").attr('id'));
        }
    );
    $('.js-action-del-button').click(
        function () {
//            var reportId = $('.delete-report-body').html();
//            alert(reportId);
            $.ajax({
                type: "POST",
                url: "/delete",
                data: {
                    reportId: reportId
                },
                dataType: 'json',
                context: this,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf_token);
                }
            }).done(function (data) {

                })
                .fail(function (data) {
                })
                .always(function (data) {
                    $('#deleteModal').modal('hide');
                    $('#' + reportId).slideUp();
                });
        }
    );
}