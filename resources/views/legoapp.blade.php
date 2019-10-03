@extends('layout.mainlayout')

@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <h1>Lego search App</h1>
            <p>This one page app uses jQuery to make AJAX calls via an api route with the Rebrickable database. The main aim is to find sets you may have based on a selection of parts from a bulk job lot. 
            The best practice is to find either unique parts and colours and work your way up from there. Due to limits in place and how the external APIs work, there is a limit of 4 parts per search.</p> 
        </div>
    </div>
</div>

<div id="legoVue">
    <div class="container-fluid legoapp-basket">
        <div class="row">
            <legoapp-basket :basdata="{!! htmlentities(json_encode($aBasket)) !!}"></legoapp-basket>
        </div>
    </div>
    <div class="container-fluid legoapp">
        <div class="row">
            <div class="col-sm-4 legosearch" id="legoapp-search-holder">
                <legoapp-search :catdata="{!! htmlentities(json_encode($aCategories)) !!}" :coldata="{!! htmlentities(json_encode($aColours)) !!}"></legoapp-search>
            </div>
            <div class="col-sm-8 legoresults">
                <legoapp-results></legoapp-results>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col">
            <h1>Code information</h1>
            <p>Before starting this task I didn't know much about modern Javascript frameworks (being mainly a back-end developer). I was going to write this application using vue.js while using jQuery for AJAX calls. 
            I realised this was a big mistake in terms of how the framework was meant to be used and as such it is mostly a jQuery application and other samples feature full vue.js examples.</p>
            <p>As the API used isn't well known I wrote the classes to interact with it from scratch. Some unique features include saving JSON responses to limit the number of calls to their API as the response 
            times can be reasonably long. As Lego items are rarely updated, data is only refreshed on a daily basis and records from previos days are deleted via a Laravel scheduled task. No user data is attached to each 
            search and it is fully sanitised to prevent any damage from potential attacks.</p>
            <p>Another stumbling block is the fact that getting the desired result has required some creative use of the API. Unfortunately it does not let you search for sets that include a set of different parts. I need to 
            fetch all sets each selected part is used in and then use PHP to calculate which sets all the parts are used in.</p> 
        </div>
    </div>
</div>

@endsection

<script type="text/javascript">
  window.onload = function () {
    const app = new Vue({
        el: "#legoVue"
    });
  }
</script>