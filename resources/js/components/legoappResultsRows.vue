<template>
    <tbody id="results-display-tbody">
        <tr v-for="row in searchdata">
            <th scope='row'>{{ row.part_num }}</th>
            <td><img :src="row.part_img_url" style='width:50px;' :alt="row.name"/></td>
            <td class='d-none d-sm-table-cell'>{{ row.name | truncate(30, '...') }}</td>
            <td class='d-none d-sm-table-cell'></td>
            <td><button class='btn btn-primary launchColor' :id="row.part_num">Select colour &amp; add</button></td>
        </tr>
    </tbody>
</template>

<script>
import Vue from 'vue';
Vue.forceUpdate();
    export default {
        props: ['searchdata'],
        mounted(){
            //$("input[name='_token']").val($('meta[name="csrf-token"]').attr('content'));
            $("button[type='submit']").click(function(){
                //$(".legoresults").show();
                var formData = $('form').serialize();

                $.ajax({
                    method: "POST",
                    url: "/api/lego/getResults",
                    data: formData,
                    dataType: "json"
                })
                .done(function( data ) {
                    this.searchdata = data.results;
                    this.$forceUpdate();
                    //$('#results-display-tbody').html('');
                    //populate(data);
                    //$('#part-search-header').after('<legoapp-results-rows :searchdata="'+data.results+'"></legoapp-results-rows>');
                    $('.lego-result').fadeIn();

                    $(".launchColor").click(function(){
                        var partId = $(this).attr('id');
                        alert(partId);
                    });
                }).fail(function(){
                    //Create error
                });
            });
        }
    }
</script>