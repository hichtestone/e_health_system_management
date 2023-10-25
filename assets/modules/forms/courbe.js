document.addEventListener('DOMContentLoaded', function(){

    function addFormToCollection($collectionHolderClass) {

        // Get the ul that holds the collection of tags
        var $collectionHolder = $('.' + $collectionHolderClass);

        // Get the data-prototype explained earlier
        var prototype = $collectionHolder.data('prototype');

        // get the new index
        var index = $collectionHolder.data('index');

        var newForm = prototype;

        newForm = newForm.replace(/__name__/g, index);
        // increase the index with one for the next item
        $collectionHolder.data('index', index + 1);
        var deleteitem=$('<div class="col-2"><button class="btn btn-primary removeDrug"><i class="fa fa-minus-circle"></i></button></div>');
        // Display the form in the page in an li, before the "Add a tag" link li
        var $newFormLi = $('<li class="item"></li>').append(newForm);
        // Add the new form at the end of the list
        $collectionHolder.append($newFormLi);
        var div = $('#courbe_points_'+index);
        div.children().contents().unwrap();
        $('#courbe_points_'+index+' .form-group').wrap($('<div class="col-5"></div>'));
        $('#courbe_points_'+index).append(deleteitem);
        $( "body" ).off( "click", ".removeDrug");
        if($('.item').length>1) {
            $("body").on("click", ".removeDrug", function () {
                $(this).closest(".item").remove();
                if ($('.item').length == 1) {
                    $('.removeDrug').removeClass('btn-primary');
                    $('.removeDrug').addClass('btn-secondary');
                    $("body").off("click", ".removeDrug");
                } else {
                    $('.removeDrug').removeClass('btn-secondary');
                    $('.removeDrug').addClass('btn-primary');
                }
            });
        }
    }

    $(function() {

        // Get the ul that holds the collection of tags
        var $tagsCollectionHolder = $('ul.points');
        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        $tagsCollectionHolder.data('index', $tagsCollectionHolder.find('input').length);

        $('body').on('click', '.add_item_link', function(e) {
            var $collectionHolderClass = $(e.currentTarget).data('collectionHolderClass');
            // add a new tag form (see next code block)
            addFormToCollection($collectionHolderClass);
            if ($('.item').length > 1) {
                $('.removeDrug').removeClass('btn-secondary');
                $('.removeDrug').addClass('btn-primary');

            }
        });
        if($('.item').length>1) {
            $("body").on("click", ".removeDrug", function () {
                $(this).closest(".item").remove();
                if ($('.item').length == 1) {
                    $('.removeDrug').removeClass('btn-primary');
                    $('.removeDrug').addClass('btn-secondary');
                    $( "body" ).off( "click", ".removeDrug");

                }

            });
        }
        else{
            $('.removeDrug').removeClass('btn-primary');
            $('.removeDrug').addClass('btn-secondary');
            $( "body" ).off( "click", ".removeDrug");
        }
    });
});
