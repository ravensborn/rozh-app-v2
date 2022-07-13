<div>


    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Home</h1>
    </div>

    <div class="row">

        <div class="col-12">
            <img src="{{ asset('img/logo.svg') }}" alt="Website Logo" width="32px">
            <span>&nbsp; A place to manage {{ env('APP_NAME') }} orders.</span>
        </div>

        <div class="col-12 mt-5">
            <a href="{{ route('orders.index') }}">Click here to go to orders.</a>
        </div>

    </div>


</div>
