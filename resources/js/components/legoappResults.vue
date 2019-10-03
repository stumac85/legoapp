<template>
    <div id="results-display">
        <h2 class="collapse" id="legoapp-results-title"></h2>
        
        <div class="row collapse" id="legoapp-loading">
            <div class="col">
                <div class="d-flex align-items-center">
                    <strong>Loading...</strong>
                    <div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
                </div>
            </div>
        </div>

        <div class="row collapse" id="legoapp-error">
            <div class="col-xl-1-12">
                <div class="alert alert-primary" role="alert">
                    <strong>Sorry, no items found. Please try a different search.</strong>
                </div>
            </div>
        </div>
        
        
        <div class="table-responsive collapse" id="legoapp-results-data">
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                    <th scope="col">Part ID</th>
                    <th scope="col">Image</th>
                    <th scope="col" class="d-none d-sm-table-cell">Name</th>
                    <th scope="col" class="d-none d-sm-table-cell">Links</th>
                    <th scope="col">Action</th>
                    </tr>
                </thead>
                <tr class="legoapp-dummy-row d-none">
                        <th scope="row" class="row-part-num"></th>
                        <td><a href="" data-toggle="lightbox" class="row-img-link" data-footer=""><img src="" style="width:50px; height:50px;" class="row-img" alt=""/></a></td>
                        <td class="d-none d-sm-table-cell row-name"></td>
                        <td class="d-none d-sm-table-cell row-links"></td>
                        <td><button class="btn btn-primary launchColor">Select colour &amp; add</button></td>
                    </tr>
                <tbody id="results-display-tbody">
                    
                </tbody>
            </table>
        </div>
        <div class="submit-box row collapse" id="legoapp-pagination">
            <div class="col align-self-end">
                <form onsubmit="return false;" id="lego-pagination-form">
                    <input type="hidden" name="inputName" value=""/>
                    <input type="hidden" name="inputCategories" value=""/>
                    <input type="hidden" name="inputColours" value=""/>
                    <input type="hidden" name="page" value="1"/>
                    <div id="pagination-holder" class="d-flex p-2 bd-highlight justify-content-center"></div>
                </form>
            </div>
        </div>
        
        <!-- ErrorModal -->
        <div class="modal fade" id="error-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Sorry, you can't do that...</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
                    <div class="modal-body" id="error-modal-body">
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ColourModal -->
        <div class="modal fade" id="color-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Select a Colour from the options below to add to your selection</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col" id="color-modal-body">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        

    </div>
</template>

<script>
    import VueHolder from 'vue-holderjs';
    Vue.use(VueHolder);
    var selectedColor = '';
    var selectedColorName = '';
    var basketCount=0;

    /***
     * 
     * function doSearch(serialized form data)
     * Called by both pressing search or using pagination
     * Functionality: 1. hides displays in the results area and shows the loading animation
     * 2. makes an AJAX call to the API, fetching any results of a query
     * 3. Runs the populate function or noResults function depending on the return from the php backend
     * 4. Runs the pagination function
     * 5. Displays the results, title and pagination
     * 
     ***/
    function doSearch(formdata){
        $('#legoapp-results-data').hide();
        $('#legoapp-error').hide();
        $('#legoapp-loading').show();
        //for mobile
        $('.legoresults').show();

        $('#results-display-tbody').html('');

        $.ajax({
                method: "POST",
                url: "/api/lego/getResults",
                data: formdata,
                dataType: "json"
        })
        .done(function( data ) {
                populate(data);
                
                if(data.count>0){
                    $('#legoapp-results-title').html('Showing '+data.count.toLocaleString()+' result(s)');
                    $('#legoapp-results-title').show();

                    //pagination
                    if(paginate(data.pages)){
                        $('#legoapp-pagination').fadeIn();
                        $('.pagination-button').click(function(){
                            $('.pagination-button').off();
                            navigateTo($(this).attr('navigate-to'));
                        });
                    } else {
                        $('#pagination-holder').html('');
                    }

                    $('[data-toggle="tooltip"]').tooltip();
                    $('#legoapp-loading').hide();
                    $('#legoapp-results-data').fadeIn();
                    
                    $('#legoapp-loading').attr('style','height:'+$('#legoapp-results-data').height()+'px; width:'+$('#legoapp-results-data').width()+'px;');
                } else {
                    noResults();
                }

                $(".launchColor").on("click", function(){
                    var partId = $(this).attr('id');
                    if(selectedColor){
                        /* call the function to add to the basket */
                        addItem(partId,selectedColor);
                    } else {
                        /* launch the modal for colour selection */
                        getColors(partId);
                    }
                });

                //Re-enable the submit button
                $("button[type='submit']").attr('disabled',false);                
        }).fail(function(){
                //Create error
                noResults();
        });
    }

    /***
     * 
     * function noResults
     * Called by doSearch
     * Functionality: Displays/Hides DOM elements needed if there are no results
     * 
     ***/
    function noResults(){
        $('#legoapp-results-title').hide();
        $('#legoapp-loading').hide();
        $('#legoapp-results-data').hide();
        $('#pagination-holder').html('');
        $('#legoapp-error').show();

    }

    /***
     * 
     * function populate(json data)
     * Called by doSearch
     * Functionality: 1. Creates a new row in the results table
     * 2. Replicates a dummy row hidden in the table
     * 3. Alters DOM elements in the replicated dummy row to display our data
     * 
     ***/
    function populate(data){
        
        $.each(data.results, function(key, element) {
            $('#results-display-tbody').append("<tr id='element" + element.part_num + "'></tr>");
            $('#element'+element.part_num).append($('.legoapp-dummy-row').html());
            var rowID = '#element'+element.part_num;

            var part = element.part_num;
            if(part.length>6){
                part = jQuery.trim(part).substring(0, 6).trim(this) + "...";
            }
            
            $(rowID).find('.row-part-num').html(part);
            
            if(element.part_img_url){
                $(rowID).find('.row-img').attr('src',element.part_img_url);
                $(rowID).find('.row-img').attr('alt',element.name);
                $(rowID).find('.row-img-link').attr('href',element.part_img_url);
                $(rowID).find('.row-img-link').attr('data-footer',element.name);
            } else {
                $(rowID).find('.row-img-link').after("<span style='height:50px; width:50px; display: block;'>None</span>");
                $(rowID).find('.row-img-link').remove();
            }
            
            var name = element.name;
            if(name.length>30){
                name = jQuery.trim(name).substring(0, 30).trim(this) + "...";
            }
            $(rowID).find('.row-name').html(name);
            
            
            var links = "<a href='" + element.part_url + "' target='_blank' rel='noopener noreferrer'>View on Rebrickable &raquo;</a>";
            if(element.external_ids.BrickLink){
                links += "<br/><a href='https://www.bricklink.com/v2/catalog/catalogitem.page?P=" + element.external_ids.BrickLink + "' target='_blank' rel='noopener noreferrer'>View on Bricklink &raquo;</a>";
            }
            $(rowID).find('.row-links').html(links);

            $(rowID).find('.launchColor').attr('id', element.part_num);
            if(selectedColor)
                $(rowID).find('.launchColor').html('Add &raquo;');
        });
    }

    /***
     * 
     * function paginate(total pages)
     * Called by doSearch
     * Functionality: Displays the pagination bar with a limit of 10 pages per display
     * 
     ***/
    function paginate(pages){
        if(pages>1){
            //get currect page
            var currentPage = parseInt($('#lego-pagination-form').find("input[name='page']").val());
            //create first (start) button(s)
            var html_code = '<div class="nav-scroller py-1 mb-2"><nav aria-label="Search Results Navigation"><ul class="pagination">';
            //Use first and last if there's more than 10 pages
            if(pages>5 && currentPage>1){
                html_code += '<li class="page-item"><a class="page-link pagination-button" href="javascript:" navigate-to="1" aria-label="First">&laquo;</a></li>';
            } else {
                html_code += '<li class="page-item disabled"><a class="page-link pagination-button" href="javascript:" aria-label="First">&laquo;</a></li>';
            }

            //Add Previous if we're over page 1
            if(currentPage>1){
                html_code += '<li class="page-item"><a class="page-link pagination-button" href="javascript:" navigate-to="'+(currentPage-1)+'" aria-label="Previous">&lt;</a></li>';
            } else {
                html_code += '<li class="page-item disabled"><a class="page-link pagination-button" href="javascript:" aria-label="Previous">&lt;</a></li>';
            }

            //if pages are more than 10 and we're on page over 10 then we only start at the nearest 10
            var i=1;
            if(currentPage>=5){
                if(currentPage%5==0)
                    i = Math.floor((currentPage+1)/5)*5;
                else
                    i = Math.floor(currentPage/5)*5;
            }

            var limit=pages;
            if(limit>5){
                if(currentPage%5==0)
                    limit = Math.ceil((currentPage+1)/5)*5;
                else
                    limit = Math.ceil(currentPage/5)*5;

                if(limit>pages)
                    limit = pages;
            }


            for(i;i<=limit;i++){
                if(i!=currentPage){
                    if(i%5!=0)
                        html_code += '<li class="page-item"><a class="page-link pagination-button" href="javascript:" navigate-to="'+i+'">'+i+'</a></li>';
                    else
                        html_code += '<li class="page-item"><a class="page-link pagination-button" href="javascript:" navigate-to="'+i+'" aria-label="More">...</a></li>';
                } else {
                    html_code += '<li class="page-item active disabled"><a class="page-link pagination-button" href="javascript:">'+i+'</a></li>';
                }
            }

            //Add next if possible
            if(currentPage<pages){
                html_code += '<li class="page-item"><a class="page-link pagination-button" href="javascript:" navigate-to="'+(currentPage+1)+'" aria-label="Next">&gt;</a></li>';
            }

            //Use first and last if there's more than 10 pages
            if(pages>5){
                html_code += '<li class="page-item"><a class="page-link pagination-button" href="javascript:" navigate-to="'+pages+'" aria-label="Last">&raquo</a></li>';
            }

            html_code += '</ul></nav></div>';
            $('#pagination-holder').html(html_code);


            return true;
        }
        return false;
    }

    /***
     * 
     * function navigateTo(page number)
     * Called by pressing any pagination link
     * Functionality: calls a new search based on the desired page number
     * 
     ***/
    function navigateTo(page){
        $('#lego-pagination-form').find("input[name='page']").val(page);
        var formData = $('#lego-pagination-form').serialize();
        doSearch(formData);
    }

    /***
     * 
     * function addItem(part_num,color_id)
     * Called by pressing the add button when a part and colour are selected
     * Functionality: 1. makes an ajax call to the back-end to fetch the part details and save the chosen part in a session
     * 2. adds the item to the basket display
     * 
     ***/
    function addItem(part_num,color_id){
        var cardID = 'card_' + part_num + color_id;
        
        if(!$("#"+cardID). length && basketCount < 8){
            $.ajax({
                    method: "POST",
                    url: "/samples/legoapp/addItem",
                    data: { part_num: part_num, color_id: color_id, color_name: selectedColorName, _token: $('meta[name="csrf-token"]').attr('content')}
            })
            .done(function( data ) {
                console.log(data);
                if(!data.hasError){
                    $('#legoapp-empty-basket').hide();
                    $('#legoapp-basket-items').show();
                    
                    $('#legoapp-dummy-basket-item').after('<div class="card collapse m-1" style="width: 10rem;" id="' + cardID + '">'+$('#legoapp-dummy-basket-item').html()+'</div>');
                    var theCard = $('#'+cardID);

                    if(data.results.part_img_url){
                        theCard.find('img').attr('src',data.results.part_img_url);
                    } else {
                        theCard.find('img').after('<span style="width:158px; height: 158px; display:block; background:#f0f0f0;">&nbsp;</span>');
                        theCard.find('img').remove();
                    }

                    theCard.find('.card-title').html(part_num);
                    theCard.find('.card-text').html('Appears in '+data.results.num_sets+' set(s)');
                    theCard.find('.legoRemoveItem').on('click', function(){
                        $(this).attr('disabled',true);
                        removeFromBasket(data.sessionData,cardID);
                    });
                    theCard.fadeIn();
                    document.getElementById("legoVue").scrollIntoView({behavior: 'smooth', block: 'start'});

                    basketCount++;
                    if(basketCount>=2)
                    {
                        $('#legoapp-basket-search').show();
                    }
                    
                } else {
                    noResults();
                }
            })
            .fail(function( data ){
                noResults();
            });
        } else {
            if($("#"+cardID). length)
                launchModal('Sorry, that part &amp; colour combination has already been selected. Please select a different part.');
            else
                launchModal('Sorry, there is a limit of 8 selections to search for. Please remove one or search from your current selection.');
        }
    }

    /***
     * 
     * function remove from basket
     * Functionality: removes a part item from the basket section when the "remove button is clicked"
     * Arrtibutes passed are identifier (data.sessionData set in addItem) and cardID (also set in addItem:  var cardID = 'card_' + part_num + color_id)
     * 1. Makes an AJAX call to remove the part from session data
     * 2. Reduces the basket count and displays a message if necessary
     * 
    ***/
    function removeFromBasket(identifier,cardID){
        
        $.ajax({
                method: "POST",
                url: "/samples/legoapp/removeItem",
                data: { identifier: identifier, _token: $('meta[name="csrf-token"]').attr('content')}
        })
        .done(function( data ) {
            if(data.status!=1)
                launchModal('Sorry, it looks like your basket may already be empty as you haven&quot;t interacted with this page for over 2 hours. Please refresh this page to restart your search.');
                
            $('#'+cardID).remove();
            basketCount--;
            if(basketCount==0){
                $('#legoapp-empty-basket').show();
                $('#legoapp-basket-items').hide();
            }
            if(basketCount<2){
                $('#legoapp-basket-search').hide();
            }            
        })
        .fail(function( data ){
            launchModal('Sorry, it looks like your basket may already be empty as you haven&quot;t interacted with this page for over 2 hours. Please refresh this page to restart your search.');
        });
    }

    /***
     * 
     * function launch modal
     * Functionality: launches a modal with the corresponding message
     * 
    ***/
    function launchModal(message){
        $("#error-modal-body").html(message);
        $('#error-modal').modal();
    }

    /***
     * 
     * function getColors
     * Functionality: displays colour options based on a Lego part number
     * 1. Makes an AJAX call to get json data of colors based on the part number (part_num)
     * 2. creates cards for each colour with the corresponding image
     * 3. displays an error if there is no colour data or part data (can happen with custom parts)
     * 
    ***/
    function getColors(part_num){
        $.ajax({
                method: "POST",
                url: "/api/lego/getColourByPart",
                data: { part_num: part_num, _token: $('meta[name="csrf-token"]').attr('content')},
                dataType: "json"
        })
        .done(function( data ) {
            if(!$.isEmptyObject(data)){
                $('#color-modal-body').html('');
                $.each(data, function(key, element) {
                    var html = '<div class="card m-1" style="width: 8rem; float:left;">';
                    if(element.img_url){
                        html += '<img src="' + element.img_url + '" class="border-bottom card-img-top" style="min-height:158px;"/>';
                    } else {
                        html += '<span style="height: 158px; display:block; background:#f0f0f0;">No Image</span>';
                    }
                    html += '<div class="card-body" style="height:162px;"><p class="card-text" style="height:69px;">' + key + '<br/>In ' + element.set_count + ' set(s)</p>';
                    html += '<button class="btn btn-primary legoAddColor" id="col_'+part_num+'_'+element.color_id+'">Select</button></div></div>';
                    $('#color-modal-body').append(html);

                    $("#"+'col_'+part_num+'_'+element.color_id).on("click", function(){
                        addColor(part_num,element.color_id,key);
                    });
                });
                    
                $('#color-modal').modal();
            } else {
                launchModal('Sorry, it looks like this item has no colours attached to it. This is often because it is not used in any active sets.');
            }      
        })
        .fail(function( data ){
            launchModal('Sorry, an internal error occured. This can be because although the part is in the Rebrickable database, it may not exist in any sets or colours.');
        });
    }

    /***
     * 
     * function add color
     * Functionality: add an item to the basket based on colour selection
     * part_num: string provided by rebrickable API (unique)
     * color_id: integer referring to the chosen colour
     * color_name: string representation of the chosen colour
     * 
    ***/
    function addColor(part_num,color_id,color_name){
        selectedColor = color_id;
        selectedColorName = color_name;
        addItem(part_num,color_id);
        selectedColor = '';
        selectedColorName = '';
        $('#color-modal').modal('hide');
    }

    export default {
        mounted(){
            /***
             * 
             * function search button click
             * Functionality: calls a new search based on the selections made within the search form
             * Serialises the search form data and runs the doSearch function with the serialised data
             * Also sets the selected colour if chosen & replicates selections within the pagination form
             * 
             ***/
            $("button[type='submit']").click(function(){
                var formData = $('#legoSearchForm').serialize();
                
                if($('#inputColours').val()){
                    selectedColor = $('#inputColours').val();
                    selectedColorName = $('#inputColours').find('option:selected').text();
                } else {
                    selectedColor = '';
                    selectedColorName = '';
                }

                //populate the pagination form
                $('#lego-pagination-form').find("input[name='inputName']").val($('#inputName').val());
                $('#lego-pagination-form').find("input[name='inputCategories']").val($('#inputCategories').val());
                $('#lego-pagination-form').find("input[name='inputColours']").val($('#inputColours').val());
                $('#lego-pagination-form').find("input[name='page']").val($('#inputPage').val());
                $("button[type='submit']").attr('disabled',true);
                doSearch(formData);
                document.getElementById("legoapp-search-holder").scrollIntoView({behavior: 'smooth', block: 'start'});
            });

            $('.legoRemoveItem').on('click', function(){removeFromBasket($(this).attr('id'),'card_'+$(this).attr('id'));});
            basketCount = $("#legoapp-basket-items > div.pre-rendered").length;
            if(basketCount>0){
                $('#legoapp-empty-basket').hide();
                $('#legoapp-basket-items').show();
                if(basketCount>=2)
                {
                    $('#legoapp-basket-search').show();
                }
            }
        }
    }
</script>