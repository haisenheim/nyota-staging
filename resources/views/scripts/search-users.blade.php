@include('scripts.tooltips')
<script>
    $(function() {
        var cardTitle = $('#card_title');
        var usersTable = $('#users_table');
        var resultsContainer = $('#search_results');
        var usersCount = $('#user_count');
        var clearSearchTrigger = $('.clear-search');
        var searchform = $('#search_users');
        var searchformInput = $('#user_search_box');
        var userPagination = $('#user_pagination');
        var searchSubmit = $('#search_trigger');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        searchform.submit(function(e) {
            e.preventDefault();
            resultsContainer.html('');
            usersTable.hide();
            clearSearchTrigger.show();
            let noResulsHtml = '<tr>' +
                                '<td>@lang("usersmanagement.search.no-results")</td>' +
                                '<td></td>' +
                                '<td class="hidden-xs"></td>' +
                                '<td class="hidden-xs"></td>' +
                                '<td class="hidden-xs"></td>' +
                                '<td class="hidden-sm hidden-xs"></td>' +
                                '<td class="hidden-sm hidden-xs hidden-md"></td>' +
                                '<td class="hidden-sm hidden-xs hidden-md"></td>' +
                                '<td></td>' +
                                '<td></td>' +
                                '<td></td>' +
                                '</tr>';

            $.ajax({
                type:'POST',
                url: "{{ route('search-users') }}",
                data: searchform.serialize(),
                success: function (result) {
                    let jsonData = JSON.parse(result);
                    if (jsonData.length != 0) {
                        $.each(jsonData, function(index, val) {
                            let rolesHtml = '';
                            let roleClass = '';
                            let editCellHtml = '<a class="btn btn-sm btn-info btn-block" href="admin/user/' + val.id + '/edit" data-toggle="tooltip" title="@lang("usersmanagement.tooltips.edit")">@lang("usersmanagement.buttons.edit")</a>';
                            let deleteCellHtml = '<form method="POST" action="/admin/user/'+ val.id +'" accept-charset="UTF-8" data-toggle="tooltip" title="Delete">' +
                                    '{!! Form::hidden("_method", "DELETE") !!}' +
                                    '{!! csrf_field() !!}' +
                                    '<button class="btn btn-danger btn-sm" type="button" style="width: 100%;" data-toggle="modal" data-target="#confirmDelete" data-title="Delete User" data-message="@lang("usersmanagement.modals.delete_user_message", ["user" => "'+val.name+'"])">' +
                                        '@lang("usersmanagement.buttons.delete")' +
                                    '</button>' +
                                '</form>';
                            let activeHtml = if(val.activated == 1){
                                '<a class="btn btn-sm btn-primary" href="route("unblock", ["id" =>' + val.id+ '])" data-toggle="tooltip">Inactive</a>';
                            }
                            else{
                                '<a class="btn btn-sm btn-danger" href="route("block", ["id" =>'+ val.id +'])" data-toggle="tooltip">Active</a>';
                            }

                            var activeclass = "";
                            if(val.activated == 0){
                                activeclass = 'Inactive';
                            }
                            else{
                                activeclass = 'Active';
                            }
                           let statusHtml = activeclass;
                            resultsContainer.append('<tr>' +
                                '<td>' + val.id + '</td>' +
                                '<td>' + val.username + '</td>' +
                                '<td class="hidden-xs">' + val.customer_code + '</td>' +
                                '<td class="hidden-xs">' + val.email + '</td>' +
                                '<td class="hidden-xs">' + statusHtml + '</td>' +
                                '<td>' + deleteCellHtml + '</td>' +
                                '<td>' + editCellHtml + '</td>' +
                                '<td>' + activeHtml + '</td>' +
                            '</tr>');
                        });
                    } else {
                        resultsContainer.append(noResulsHtml);
                    };
                    usersCount.html(jsonData.length + " @lang('usersmanagement.search.found-footer')");
                    userPagination.hide();
                    cardTitle.html("@lang('usersmanagement.search.title')");
                },
                error: function (response, status, error) {
                    if (response.status === 422) {
                        resultsContainer.append(noResulsHtml);
                        usersCount.html(0 + "@lang('usersmanagement.search.found-footer')");
                        userPagination.hide();
                        cardTitle.html("@lang('usersmanagement.search.title')");
                    };
                },
            });
        });
        searchSubmit.click(function(event) {
            event.preventDefault();
            searchform.submit();
        });
        searchformInput.keyup(function(event) {
            if ($('#user_search_box').val() != '') {
                clearSearchTrigger.show();
            } else {
                clearSearchTrigger.hide();
                resultsContainer.html('');
                usersTable.show();
                cardTitle.html("@lang('usersmanagement.showing-all-users')");
                userPagination.show();
                usersCount.html(" ");
            };
        });
        clearSearchTrigger.click(function(e) {
            e.preventDefault();
            clearSearchTrigger.hide();
            usersTable.show();
            resultsContainer.html('');
            searchformInput.val('');
            cardTitle.html("@lang('usersmanagement.showing-all-users')");
            userPagination.show();
            usersCount.html(" ");
        });
    });
</script>
