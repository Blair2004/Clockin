@extends( 'layout.dashboard' )

@section( 'layout.dashboard.body' )
    <div class="h-full flex flex-col flex-auto">
        @include( '../common/dashboard-header' )
        <div class="px-4 flex-auto flex flex-col" id="dashboard-content">
            <ck-timer></ck-timer>
        </div>
    </div>
@endsection

@section( 'layout.dashboard.footer.inject' )
    <script src="{{ asset( 'modules/clockin/dist/vendor.js' ) }}"></script>
    <script src="{{ asset( 'modules/clockin/dist/manifest.js' ) }}"></script>
    <script src="{{ asset( 'modules/clockin/dist/bootstrap.js' ) }}"></script>
@endsection