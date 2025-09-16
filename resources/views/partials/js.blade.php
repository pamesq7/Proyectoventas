<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

{{-- Scripts de la aplicación --}}
<script src="{{ asset('adminlte/js/scripts.js')}}" ></script>

{{-- Chart.js solo se carga en páginas que lo necesitan --}}
@if(request()->routeIs('dashboard') || request()->routeIs('reportes.*'))
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"  crossorigin="anonymous"></script>
<script src="{{ asset('adminlte/assets/demo/chart-area-demo.js')}}" ></script>
<script src="{{ asset('adminlte/assets/demo/chart-bar-demo.js')}}" ></script>
@endif

{{-- DataTables --}}
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"  crossorigin="anonymous"></script>
<script src="{{ asset('adminlte/js/datatables-simple-demo.js')}}" ></script>