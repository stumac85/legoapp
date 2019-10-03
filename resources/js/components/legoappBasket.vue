<template>
<div id="legoapp-basket">
    <div class="row" id="legoapp-empty-basket">
            <p class="p-2 m-5 col">Your selected items will appear here. Use the search below and then select a part and colour. If you select a colour in the search then you can instantly add the item here.</p>
    </div>
    <div class="row">
        <div class="collapse col" id="legoapp-basket-items">
            <h3 class="p-3">Your selected parts</h3>
            <div class="card collapse m-1" style="width: 10rem;" id="legoapp-dummy-basket-item">
                <img src="#" style="width:158px; height: 158px;" class="border-bottom"/>
                <div class="card-body">
                    <h5 class="card-title"></h5>
                    <p class="card-text"></p>
                    <button class="btn btn-primary legoRemoveItem">Remove</button>
                </div>
            </div>

            <div v-for="(bData, key) in basdata" class="pre-rendered card m-1" style="width: 10rem;" :id="'card_'+key">
                <img v-if="bData.part_img_url" :src="bData.part_img_url" style="width:158px; height: 158px;" class="border-bottom"/>
                <span v-if="!bData.part_img_url" style="width:158px; height: 158px; display:block; background:#f0f0f0;">&nbsp;</span>
                <div class="card-body">
                    <h5 class="card-title">{{ bData.part_num }}</h5>
                    <p class="card-text">Appears in {{ bData.num_sets }} set(s)</p>
                    <button class="btn btn-primary legoRemoveItem" :id="key">Remove</button>
                </div>
            </div>
        </div>
    </div>
    <div class="collapse row" id="legoapp-basket-search">
        <div class="col">
            <div class="submit-box-sets col align-self-end">
                <button @click="searchParts" class="btn btn-primary" id="legoapp-final-search">Search for matching set(s)</button>
            </div>
        </div>
    </div>
    
    <!-- ResultsModal -->
    <div class="modal fade" id="results-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" id="legoapp-results-modal">
                
            </div>
        </div>
    </div>

</div>
</template>

<script>
    function launchModal(message){
        $("#error-modal-body").html(message);
        $('#error-modal').modal();
    }

    export default {
        props: ['basdata'],
        methods: {
            searchParts: function(){
                $('#legoapp-final-search').attr('disabled',true);
                $('#legoapp-final-search').html('<div class="d-flex align-items-center"><strong>Loading...</strong><div class="spinner-border ml-auto" role="status" aria-hidden="true"></div></div>');
                $.ajax({
                        method: "POST",
                        url: "/samples/legoapp/getSets",
                        data: { _token: $('meta[name="csrf-token"]').attr('content')}
                })
                .done(function( data ) {
                    $('#legoapp-results-modal').html(data);
                    $('#results-modal').modal();
                    $('#legoapp-final-search').html('Search for matching set(s)');      
                    $('#legoapp-final-search').attr('disabled',false);        
                })
                .fail(function( data ){
                    launchModal('Sorry, it looks like your basket may already be empty as you haven&quot;t interacted with this page for over 2 hours. Please refresh this page to restart your search.');
                });
            }
        }
    }
</script>