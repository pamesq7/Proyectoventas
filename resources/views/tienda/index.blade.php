<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online - Mi Empresa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <!-- Barra superior -->
        <div class="bg-gray-100 py-2">
            <div class="container mx-auto px-4">
                <div class="flex justify-end space-x-6 text-sm">
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 transition duration-200">
                        <i class="fas fa-user mr-1"></i>Iniciar sesión
                    </a>
                    <a href="https://wa.me/123456789" class="text-gray-600 hover:text-green-600 transition duration-200">
                        <i class="fab fa-whatsapp mr-1"></i>WhatsApp directo
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Navegación principal -->
        <div class="container mx-auto px-4 py-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <!-- Logo -->
                <div class="text-2xl font-bold text-blue-600 mb-4 md:mb-0">
                    <a href="{{ url('/') }}">MiEmpresa</a>
                </div>
                
                <!-- Menú de navegación -->
                <nav class="flex space-x-6">
                    <a href="#servicios" class="text-gray-600 hover:text-blue-600 transition duration-200">Servicios</a>
                    <a href="{{ url('/') }}" class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-1">Tienda</a>
                    <a href="#contacto" class="text-gray-600 hover:text-blue-600 transition duration-200">Contáctanos</a>
                    <a href="#empresa" class="text-gray-600 hover:text-blue-600 transition duration-200">Empresa</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-bg text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Bienvenido a Nuestra Tienda</h1>
            <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                Descubre productos de alta calidad para todas tus necesidades
            </p>
            <a href="#productos" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-200">
                Explorar Productos
            </a>
        </div>
    </section>

    <!-- Productos de la Tienda -->
    <section id="productos" class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-4 text-gray-800">Nuestros Productos</h2>
            <p class="text-gray-600 text-center mb-12 max-w-2xl mx-auto">
                Explora nuestra amplia selección de productos cuidadosamente elegidos para ti
            </p>
            
            <!-- Filtros y Búsqueda -->
            <div class="mb-12 bg-white p-6 rounded-lg shadow-sm">
                <form method="GET" action="{{ url('/') }}" class="flex flex-col md:flex-row gap-4 items-center">
                    <!-- Búsqueda -->
                    <div class="flex-1 w-full">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Buscar productos por nombre o descripción..." 
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <!-- Botón buscar -->
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200">
                        <i class="fas fa-search mr-2"></i>Buscar
                    </button>
                    
                    <!-- Limpiar filtros -->
                    @if(request('search') || request('categoria'))
                    <a href="{{ url('/') }}" class="text-gray-600 hover:text-gray-800 px-4 py-3 border border-gray-300 rounded-lg transition duration-200">
                        <i class="fas fa-times mr-2"></i>Limpiar
                    </a>
                    @endif
                </form>
                
                <!-- Filtros por categoría -->
                @if(isset($categorias) && $categorias->count() > 0)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">Filtrar por categoría:</h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ url('/') }}" 
                           class="px-4 py-2 rounded-full border {{ !request('categoria') ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:border-blue-600' }} transition duration-200">
                            Todos los productos
                        </a>
                        @foreach($categorias as $categoria)
                        <a href="{{ url('/') }}?categoria={{ $categoria->id }}" 
                           class="px-4 py-2 rounded-full border {{ request('categoria') == $categoria->id ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:border-blue-600' }} transition duration-200">
                            {{ $categoria->nombre }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Grid de Productos -->
            @if($productos->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @foreach($productos as $producto)
                <div class="bg-white rounded-xl shadow-md overflow-hidden product-card">
                    <!-- Imagen del producto -->
                    <div class="h-48 bg-gray-100 flex items-center justify-center relative">
                        @if($producto->imagen)
                            <img src="{{ asset('storage/' . $producto->imagen) }}" 
                                 alt="{{ $producto->nombre }}" 
                                 class="h-full w-full object-cover">
                        @else
                            <div class="text-gray-400 text-center">
                                <i class="fas fa-image text-5xl mb-3"></i>
                                <p class="text-sm">Imagen no disponible</p>
                            </div>
                        @endif
                        
                        <!-- Badge de categoría -->
                        <span class="absolute top-3 left-3 text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-white bg-blue-600 bg-opacity-90">
                            {{ $producto->categoria->nombre ?? 'General' }}
                        </span>
                    </div>
                    
                    <!-- Información del producto -->
                    <div class="p-6">
                        <h3 class="font-bold text-lg mb-2 text-gray-800 line-clamp-2">{{ $producto->nombre }}</h3>
                        
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            {{ Str::limit($producto->descripcion, 80) }}
                        </p>
                        
                        <!-- Precio y stock -->
                        <div class="flex justify-between items-center mb-4">
                            <span class="font-bold text-xl text-gray-900">${{ number_format($producto->precio, 2) }}</span>
                            @if($producto->stock > 0)
                                <span class="text-sm text-green-600 font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i>En stock
                                </span>
                            @else
                                <span class="text-sm text-red-600 font-semibold">
                                    <i class="fas fa-times-circle mr-1"></i>Agotado
                                </span>
                            @endif
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="flex space-x-2">
                            <a href="{{ route('producto.show', $producto->id) }}" 
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg text-sm transition duration-200">
                                <i class="fas fa-eye mr-1"></i>Ver Detalles
                            </a>
                            <button onclick="contactarWhatsApp('{{ $producto->nombre }}')" 
                                    class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-sm transition duration-200"
                                    {{ $producto->stock <= 0 ? 'disabled' : '' }}>
                                <i class="fab fa-whatsapp mr-1"></i>Consultar
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Paginación -->
            <div class="mt-12 flex justify-center">
                {{ $productos->links() }}
            </div>
            
            @else
            <!-- Estado vacío -->
            <div class="text-center py-16">
                <div class="bg-white rounded-xl shadow-sm p-12 max-w-md mx-auto">
                    <i class="fas fa-search text-6xl text-gray-300 mb-6"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No se encontraron productos</h3>
                    <p class="text-gray-500 mb-6">Intenta con otros términos de búsqueda o categorías</p>
                    <a href="{{ url('/') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200">
                        <i class="fas fa-redo mr-2"></i>Ver todos los productos
                    </a>
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Columna 1: Información de la empresa -->
                <div>
                    <h3 class="text-xl font-bold mb-4 text-white">MiEmpresa</h3>
                    <p class="text-gray-400 mb-4">
                        Líder en calidad y servicio. Ofrecemos los mejores productos con garantía y soporte excepcional.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition duration-200">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition duration-200">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition duration-200">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Columna 2: Enlaces rápidos -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-white">Enlaces Rápidos</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/') }}" class="text-gray-400 hover:text-white transition duration-200">Inicio</a></li>
                        <li><a href="#productos" class="text-gray-400 hover:text-white transition duration-200">Tienda</a></li>
                        <li><a href="#servicios" class="text-gray-400 hover:text-white transition duration-200">Servicios</a></li>
                        <li><a href="#contacto" class="text-gray-400 hover:text-white transition duration-200">Contacto</a></li>
                    </ul>
                </div>
                
                <!-- Columna 3: Categorías -->
                @if(isset($categorias) && $categorias->count() > 0)
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-white">Categorías</h3>
                    <ul class="space-y-2">
                        @foreach($categorias->take(5) as $categoria)
                        <li>
                            <a href="{{ url('/') }}?categoria={{ $categoria->id }}" class="text-gray-400 hover:text-white transition duration-200">
                                {{ $categoria->nombre }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <!-- Columna 4: Contacto -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-white">Contacto</h3>
                    <ul class="space-y-3 text-gray-400">
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-3 text-blue-400"></i>
                            <span>+1 234 567 890</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-blue-400"></i>
                            <span>info@miempresa.com</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-3 text-blue-400"></i>
                            <span>Ciudad, País</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fab fa-whatsapp mr-3 text-green-400"></i>
                            <span>+1 234 567 890</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Línea divisoria y copyright -->
            <div class="border-t border-gray-700 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} MiEmpresa. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Script para WhatsApp -->
    <script>
        function contactarWhatsApp(productName) {
            const message = `Hola, me interesa el producto: ${productName}. ¿Podrían darme más información?`;
            const whatsappUrl = `https://wa.me/123456789?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
        }
        
        // Smooth scroll para enlaces internos
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>