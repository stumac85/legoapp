<template>
    <div id="componentSearch">
        <h5>Parts Search</h5>
        <form onsubmit="return false;" id="legoSearchForm">
            <input type="hidden" name="_token" value="">
            <div class="form-group row">
                <label for="inputName" class="col-sm-4 col-form-label">Part name</label>
                <div class="col-sm-8">
                <input type="text" class="form-control" id="inputName" name="inputName" placeholder="Part name">
                </div>
            </div>
            <div class="form-group row">
                <label for="inputCategory" class="col-sm-4 col-form-label">Category</label>
                <div class="col-sm-8">
                    <select class="custom-select" id="inputCategories" name="inputCategories">
                        <option value="" selected>Any</option>
                        <option v-for="category in catdata" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputColours" class="col-sm-4 col-form-label">Colour</label>
                <div class="col-sm-8">
                    <select class="custom-select" id="inputColours" name="inputColours">
                        <option value="" selected>Any</option>
                        <option v-for="color in coldata" :value="color.id">
                            {{ color.name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <div class="submit-box col align-self-end">
                    <input type="hidden" name="page" id="inputPage" value="1"> 
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        props: ['catdata','coldata'],
        mounted(){
            $("input[name='_token']").val($('meta[name="csrf-token"]').attr('content'));
        }
    }
</script>