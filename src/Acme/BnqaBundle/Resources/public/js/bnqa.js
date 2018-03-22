$(document).ready(function () {
    var page = 1;
    var itemPage = 1;
    var total = 0;
    var totalPage = 0;
    var type = 'main';
//    つづきをよむ
    var reportId;
    $(window).load(function () {
        if ($('#nav-tabs #main').find('.numOfType').text() < 21 && controller != 'home') {
            $('.load-to-read').hide();
        }
        total = $('#nav-tabs #main').find('.numOfType').text();
        totalPage = Math.floor(total / 20);
        if (typeof asinCode !== "undefined" && asinCode !== null) {
            $.ajax({
                type: "GET",
                url: "/item/similarities",
                data: {
                    asin_code: asinCode
                },
                dataType: 'json'
            })
                .done(function (data) {
                    $('.similarities').html(data);
                })
                .fail(function (data) {
                    $('.similarities').html('読み込みエラー');
                })
                .always(function (data) {
                    $('.similarities').html(data);
                    bookmark();
                    followOrRemove();
                    followButton();
                });
        } else {
            bookmark();
            followOrRemove();
            followButton();
        }
        editorItem();
        editorReply();
        editorDelete();
    });


    $('#editReportBook').on('show.bs.modal', function () {
        if ($('#reportBook_type :selected').attr('value') != '4') {
            $('.misprint-form').hide();
        }
    });

    $('.js-action-show-following').click(function () {
        $('.following-sidebar').appendTo($('#followingModalBody'));
    });
    $('#followingModal').on('show.bs.modal', function () {

    });

    $('#reportBook_type').change(function () {
        var val = $('#reportBook_type :selected').attr('value');
        if (val == '4') {
//                $('.misprint-form').slideUp();
            $('.misprint-form').show();
            $('.body-label').text('正しい表記');
        } else {
            $('.misprint-form').hide();
            $('.body-label').text('本文');
            $('.misprint-form #reportBook_misprint_wrongBody').val('');
        }
    });


    $('.load-to-read').click(function () {
        if ($(this).hasClass('disabled') == false) {
            page++;
            $.ajax({
                type: "GET",
                url: "/inner/reports/line",
                data: {
                    controller: controller,
                    type: type,
                    page: page,
                    userId: userId,
                    asinCode: asinCode,
                    bookPage: bookPage
                },
                dataType: 'json'
            })
                .done(function (data, status, xhr) {
                    $('#reports-line').append(data);
                    $('.load-to-read').show();
                    $('.load-to-read').appendTo('#homebody');
                    totalPage--;
                    if (totalPage == 0 || (controller == 'home' && xhr.status == 204)) {
                        $('.load-to-read').hide();
                        if (controller == 'home' && xhr.status == 204) {
                            $('#reports-line').append('<div class="unit" style="text-align: center">すべて読み込みました</div>');
                        }
                    } else {
                        bookmark();
                        editorReply()
                    }

                })
                .fail(function (data) {
                    alert('error');
                });
        }
        return false
    });
    $('.show-items').click(function () {
        $('.following-sidebar').removeClass('.visible-lg');
        $(this).addClass('.visible-lg');
    });
    $('.load-items').click(function () {
        if ($(this).hasClass('disabled') == false) {
            itemPage++;
            $.ajax({
                type: "GET",
                url: "/user/homeSidebar/following/",
                data: {
                    page: itemPage
                },
                dataType: 'json'
            })
                .done(function (data, status, xhr) {
                    $('.sidebar-body').append(data);
//                    $('.load-items').show();
                    $('.load-items').appendTo('.sidebar-body');
                    if (xhr.status == 204) {
                        $('.load-items').hide();
                        $('.sidebar-body').append('<div class="unit" style="text-align: center">すべて読み込みました</div>');
                    } else {
                        followOrRemove();
                        editorItem();
                    }
                })
                .fail(function (data) {
                    alert('error');
                });
        }
        return false
    });
    editorDelete();
    $('#nav-tabs li').click(
        function () {
            if ($(this).hasClass('active')) {
                return false;
            }
            var typeCount = $(this).find('.numOfType').text();
            if (typeCount != 0 || $(this).attr("id") == 'bookmark' || controller == 'home' || controller == 'public') {
                total = typeCount;
                totalPage = Math.floor(total / 20);
                type = $(this).attr("id");
                $.ajax({
                    type: "GET",
                    url: "/inner/reports/line",
                    data: {
                        controller: controller,
                        type: type,
                        userId: userId,
                        asinCode: asinCode,
                        bookPage: bookPage
                    },
                    dataType: 'json'
                })
                    .done(function (data) {
                        $('#reports-line').empty();
                        $('#reports-line').append(data);
                        page = 1;
                        if (typeCount < 20 && controller != 'home') {
                            $('.load-to-read').hide();
                        } else {
                            $('.load-to-read').show();
                        }
                        bookmark();
                    })
                    .fail(function (data) {
                        $('#reports-line').empty();
                        $('#reports-line').html('読み込みエラー');
                    })
                    .always(function (data) {
                        $('#reports-line').empty();
                        $('#reports-line').html(data);
                        bookmark();
                    });
            } else {
                $('#reports-line').empty();
                $('#reports-line').html('<div class="unit">投稿はありません</div>');
            }
            $('#nav-tabs li').removeClass('active');
            $(this).addClass('active');
            return false;
        }
    );

});

