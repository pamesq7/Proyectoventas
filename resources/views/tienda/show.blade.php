<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $producto->nombre }} - Tienda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Header (mismo que en index) -->
    <header class="bg-white shadow-sm">
        <div class="bg-gray-100 py-2">
            <div class="container mx-auto px-4">
                <div class="flex justify-end space-x-6 text-sm">
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600">
                        <i class="fas fa-user mr-1"></i>Iniciar sesión
                    </a>
                    <a href="https://wa.me/123456789" class="text-gray-600 hover:text-green-600">
                        <i class="fab fa-whatsapp mr-1"></i>WhatsApp directo
                    </a>
                </div>
            </div>
        </div>
        
        <div class="container mx-auto px-4 py-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-2xl font-bold text-blue-600 mb-4 md:mb-0">
                    <a href="{{ url('/') }}">MiEmpresa</a>
                </div>
                
                <nav class="flex space-x-6">
                    <a href="#servicios" class="text-gray-600 hover:text-blue-600">Servicios</a>
                    <a href="{{ url('/') }}" class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-1">Tienda</a>
                    <a href="#contacto" class="text-gray-600 hover:text-blue-600">Contáctanos</a>
                    <a href="#empresa" class="text-gray-600 hover:text-blue-600">Empresa</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Detalle del Producto -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="md:flex">
                    <!-- Imagen del producto -->
                    <div class="md:w-1/2">
                        @if($producto->imagen)
                        <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="w-full h-96 object-cover">
                        @else
                        <div class="w-full h-96 bg-gray-200 flex items-center justify-center">
                            <div class="text-center text-gray-400">
                                <i class="fas fa-image text-6xl mb-4"></i>
                                <p>Imagen no disponible</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Información del producto -->
                    <div class="md:w-1/2 p-8">
                        <span class="text-sm font-semibold inline-block py-1 px-3 uppercase rounded-full text-blue-600 bg-blue-100">
                            {{ $producto->categoria->nombre ?? 'General' }}
                        </span>
                        
                        <h1 class="text-3xl font-bold mt-4 mb-2 text-gray-800">{{ $producto->nombre }}</h1>
                        
                        <div class="text-2xl font-bold text-gray-900 mb-4">${{ number_format($producto->precio, 2) }}</div>
                        
                        <p class="text-gray-600 mb-6">{{ $producto->descripcion }}</p>
                        
                        <!-- Stock -->
                        <div class="mb-6">
                            @if($producto->stock > 0)
                            <span class="text-green-600 font-semibold">
                                <i class="fas fa-check-circle mr-1"></i> En stock ({{ $producto->stock }} disponibles)
                            </span>
                            @else
                            <span class="text-red-600 font-semibold">
                                <i class="fas fa-times-circle mr-1"></i> Agotado
                            </span>
                            @endif
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="flex space-x-4">
                            <a href="https://wa.me/123456789?text=Hola, me interesa el producto: {{ urlencode($producto->nombre) }}. ¿Podrían darme más información?" 
                               target="_blank"
                               class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-lg text-center transition duration-200">
                                <i class="fab fa-whatsapp mr-2"></i> Consultar por WhatsApp
                            </a>
                            
                            <a href="{{ url('/') }}" 
                               class="bg-gray-600 hover:bg-gray-700 text-white py-3 px-6 rounded-lg text-center transition duration-200">
                                <i class="fas fa-arrow-left mr-2"></i> Volver a la Tienda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">MiEmpresa</h3>
                    <p class="text-gray-400">Ofrecemos los mejores productos con calidad garantizada y servicio al cliente excepcional.</p>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Enlaces Rápidos</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/') }}" class="text-gray-400 hover:text-white">Inicio</a></li>
                        <li><a href="{{ url('/') }}" class="text-gray-400 hover:text-white">Tienda</a></li>
                        <li><a href="#contacto" class="text-gray-400 hover:text-white">Contacto</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contacto</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><i class="fas fa-phone mr-2"></i> +1 234 567 890</li>
                        <li><i class="fas fa-envelope mr-2"></i> info@miempresa.com</li>
                        <li><i class="fas fa-map-marker-alt mr-2"></i> Dirección de la empresa</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} MiEmpresa. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>