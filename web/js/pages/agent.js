(function ($) {
    var collectionContainer = $('.agent-districts-collection');
    var prototype = collectionContainer.data('prototype');
    var agentDistricts = $('.agent-district-widget', collectionContainer);
    var index = agentDistricts.length;
    var districtsSelect = $('select.districts');
    var chosen = districtsSelect.chosen().data('chosen');

    /* improve performance of chosen */
    chosen.search_results.unbind('mouseup.chosen');
    chosen.search_results.bind('mouseup.chosen', function(e) {
        chosen.search_results_mouseup(e);
    });

    /* disable district director checkbox if he is already set */
    agentDistricts.each(function () {
        var $widget = $(this);
        var district_input = $('input.district-id', $widget);
        var district_id = district_input.val();
        var has_district_director = $('[value="'+district_id+'"]', districtsSelect).attr('disable-district-director');

        if (has_district_director) {
            $widget.find('.district-director').prop('checked', false);
            $widget.find('.district-director').attr('disabled', 'disabled');
        }
    });


    /* handle remove\add district on chosen select */
    districtsSelect.on('change', function (e, params) {
        var district_id;

        if ('selected' in params) {
            /* add new district */
            district_id = params['selected'];
            var selected_option = $('[value="'+district_id+'"]', districtsSelect);
            var district_name = selected_option.text();
            var disabled_district_director = selected_option.attr('disable-district-director');

            /* render new agent district widget */
            index++;
            var html = prototype.replace(/__name__/g, index);

            var $widget = $(html);
                $widget.attr('data-district-id', district_id);
                $widget.find('input.district-id').val(parseInt(district_id));
                $widget.find('p').html('<strong>'+district_name+'</strong>');
                $widget.hide();
            
            if (disabled_district_director) {
                $widget.find('.district-director').attr('disabled', 'disabled');
            }

            collectionContainer.prepend($widget);

            $widget.fadeIn(400);

        } else if ('deselected' in params) {
            /* remove district */
            district_id = params['deselected'];
            $('[data-district-id="'+district_id+'"]', collectionContainer).remove();
        }
    });

    /* handle removing district click on red "x" */
    collectionContainer.on('click', '.remove-agent-district', function () {
        var $widget = $(this).closest('.agent-district-widget');
        var district_id = $widget.attr('data-district-id');

        $('[value="'+district_id+'"]', districtsSelect).removeAttr('selected');
        districtsSelect.trigger('chosen:updated');

        $widget.remove();
    });
})(jQuery);