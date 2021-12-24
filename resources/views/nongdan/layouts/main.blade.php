<!DOCTYPE html>
<html>
@include('nongdan.layouts.header')
<body class="layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
@include('nongdan.layouts.navbar')
<!-- /.navbar -->

    <!-- Main Sidebar Container -->
@include('nongdan.layouts.sidebar')

<!-- Content Wrapper. Contains page content -->
@section('body') @show
<!-- /.content-wrapper -->
{{--@include('nongdan.layouts.foot')--}}
<!-- ./wrapper -->

@include('nongdan.layouts.footer')
</body>
</html>
