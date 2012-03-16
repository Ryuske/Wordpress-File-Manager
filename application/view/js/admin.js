/*
 * Variable defintions
 */
var file_manager = {'sortable_original_order': null, 'ul_order': Array()};

/*
 * Add tabs for navigation
 */
jQuery(document).ready(function(){
    jQuery('#option-tabs').tabs();
    jQuery("#option-tabs").bind("tabsshow", function(event, ui) { 
        window.location.hash = ui.tab.hash;
    })
});

/*
 * Add accordian to Categories page in backend
 */
jQuery(function() {
    var stop = false;
    jQuery('.accordion h3').click(function(event) {
        if (stop) {
            event.stopImmediatePropagation();
            event.preventDefault();
            stop = false;
        }
    });
    jQuery('.accordion')
    .accordion({
        header: "> div > h3",
        autoHeight: false,
        collapsible: true,
        active: false
    })
    .sortable({
        axis: "y",
        handle: "h3",
        update: function() {
            /***********************READ ME***************************
             * DONE - Need to change sortable('toArray') to the whole accordion and not just the recursion. 
             * add html form
             * Add PHP backend
             */
            var new_order = jQuery('#accordion_content').sortable('toArray');
            /*var temp_indexOf;
            var iterations=0;
            for (var element in new_order) {
                temp_indexOf = file_manager.sortable_original_order.indexOf(new_order[element]);
                jQuery('#sortable_trash div').children().eq(iterations).html(file_manager.ul_order[temp_indexOf]);
                iterations++;
            }*/

            file_manager.sortable_original_order = new_order;
            /*file_manager.ul_order = Array();
            for (var i=0; i<file_manager.sortable_original_order.length; i++) {
                file_manager.ul_order.push(jQuery('#sortable_trash div').children().eq(i).html());
            }*/
            
            jQuery('#update_category_order #new_order').val(new_order);
            //jQuery('#update_category_order').submit();
        },
        stop: function(event, ui) {
            ui.item.children( "h3" ).triggerHandler( "focusout" );
            stop = true;
        }
    });
});

//Begin block for links within an accordion
jQuery('.accordion h3 .delete').click(function() {
    window.location = jQuery(this).attr('href');
});
jQuery('.accordion h3 .update').click(function() {
    window.location = jQuery(this).attr('href');
});
jQuery('.accordion h3 .add').click(function() {
    window.location = jQuery(this).attr('href');
});
//End block

/*
 * Add sortable content, used on Belts & Programs to make belts sortable.
 */
jQuery('#sortable').sortable({
    placeholder: 'ui-state-highlight file_manager_sortable_placeholder',
    update: function(browserEvent, item) {
        var new_order = jQuery(this).sortable('toArray');
        var temp_indexOf;
        var iterations=0;

        for (var element in new_order) {
            temp_indexOf = file_manager.sortable_original_order.indexOf(new_order[element]);
            jQuery('#sortable_trash div').children().eq(iterations).html(file_manager.ul_order[temp_indexOf]);
            iterations++;
        }

        file_manager.sortable_original_order = new_order;
        file_manager.ul_order = Array();
        for (var i=0; i<file_manager.sortable_original_order.length; i++) {
            file_manager.ul_order.push(jQuery('#sortable_trash div').children().eq(i).html());
        }

        jQuery('#update_belt_order #new_order').val(new_order);
        jQuery('#update_belt_order').submit();
    }
});
jQuery('#sortable').disableSelection();

file_manager.sortable_original_order = jQuery('#sortable').sortable('toArray');
for (var i=0; i<file_manager.sortable_original_order.length; i++) {
    file_manager.ul_order.push(jQuery('#sortable_trash div').children().eq(i).html());
}

/*
 * Begin adding dialogs for jQuery UI
 */
jQuery('#update_file').dialog({
    autoOpen: false,
    width: 350,
    modal: true,
    resizable: false,
    buttons: {
        Update: function() {
            jQuery('#edit_file').submit();
        },
        Cancel: function() {
            window.location = 'plugins.php?page=file_manager#file_permissions';
        }
    }
}); //End #update_permission

jQuery('#add_category').dialog({
    autoOpen: false,
    width: 350,
    modal: true,
    resizable: false,
    buttons: {
        Add: function() {
            if (jQuery('#add_category_form #category').val() !== '') {
                jQuery('#add_category_form').submit();
            } else {
                jQuery('#add_category_notification').css('display', 'block');
            }
        },
        Cancel: function() {
            jQuery('#add_category_notification').css('display', 'none');
            jQuery(this).dialog('close');
        }
    }
}); //End #add_category

jQuery('#add_subcategory').dialog({
    autoOpen: false,
    width: 350,
    modal: true,
    resizable: false,
    buttons: {
        Add: function() {
            if (jQuery('#add_subcategory_form #category').val() !== '') {
                jQuery('#add_subcategory_form').submit();
            } else {
                jQuery('#add_subcategory_notification').css('display', 'block');
            }
        },
        Cancel: function() {
            jQuery('#add_subcategory_notification').css('display', 'none');
            window.location = 'plugins.php?page=file_manager#categories';
        }
    }
}); //End #add_subcategory


jQuery('#update_category').dialog({
    autoOpen: false,
    width: 350,
    height: 'auto',
    modal: true,
    resizable: false,
    buttons: {
        Update: function() {
            if (jQuery('#update_category_form #category').val() !== '') {
                jQuery('#update_category_form').submit();
            } else {
                jQuery('#update_category_notification').css('display', 'block');
            }
        },
        Cancel: function() {
            jQuery('#update_category_notification').css('display', 'none');
            window.location = 'plugins.php?page=file_manager#categories';
        }
    }
}); //End #update_category

jQuery('#delete_category').dialog({
        autoOpen: false,
        position: ['center', 100],
        height: 140,
        modal: true,
        resizable: false,
        buttons: {
            Delete: function() {
                jQuery('#delete_category_form').submit();
            },
            Close: function() {
                 window.location = 'plugins.php?page=file_manager#categories';
            }
        }
}); //End #delete_category
