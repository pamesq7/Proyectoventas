<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Opcion;
use App\Models\Caracteristica;

class OpcionesCaracteristicasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir opciones y sus características
        $opciones = [
            [
                'nombre' => 'Cuello',
                'descripcion' => 'Tipos de cuello disponibles para prendas deportivas',
                'caracteristicas' => [
                    ['nombre' => 'V', 'descripcion' => 'Cuello en forma de V, ideal para look casual'],
                    ['nombre' => 'Redondo', 'descripcion' => 'Cuello redondo clásico, versátil y cómodo'],
                    ['nombre' => 'Polo', 'descripcion' => 'Cuello tipo polo con botones, elegante y deportivo'],
                    ['nombre' => 'Capucha', 'descripcion' => 'Con capucha, perfecto para entrenamientos'],
                    ['nombre' => 'Tortuga', 'descripcion' => 'Cuello alto tipo tortuga, ideal para clima frío'],
                ]
            ],
            [
                'nombre' => 'Manga',
                'descripcion' => 'Longitud y estilo de mangas para diferentes necesidades',
                'caracteristicas' => [
                    ['nombre' => 'Corta', 'descripcion' => 'Manga corta, ideal para clima cálido y actividad intensa'],
                    ['nombre' => 'Larga', 'descripcion' => 'Manga larga, protección completa del brazo'],
                    ['nombre' => '3/4', 'descripcion' => 'Manga tres cuartos, equilibrio entre protección y frescura'],
                    ['nombre' => 'Sin manga', 'descripcion' => 'Sin mangas, máxima libertad de movimiento'],
                    ['nombre' => 'Raglan', 'descripcion' => 'Manga raglan, corte diagonal desde cuello a axila'],
                ]
            ],
            [
                'nombre' => 'Material',
                'descripcion' => 'Tipos de tela y material de fabricación',
                'caracteristicas' => [
                    ['nombre' => 'Algodón', 'descripcion' => '100% algodón, suave y transpirable'],
                    ['nombre' => 'Poliéster', 'descripcion' => '100% poliéster, resistente y de secado rápido'],
                    ['nombre' => 'Dri-Fit', 'descripcion' => 'Tecnología Dri-Fit, absorbe humedad y mantiene seco'],
                    ['nombre' => 'Algodón-Poliéster', 'descripcion' => 'Mezcla 50/50, combina suavidad y durabilidad'],
                    ['nombre' => 'Microfibra', 'descripcion' => 'Microfibra ultra suave, ideal para sublimación'],
                ]
            ],
            [
                'nombre' => 'Talla',
                'descripcion' => 'Tallas disponibles para todas las edades',
                'caracteristicas' => [
                    ['nombre' => 'XS', 'descripcion' => 'Extra pequeña, para personas de complexión muy delgada'],
                    ['nombre' => 'S', 'descripcion' => 'Pequeña, talla estándar para personas delgadas'],
                    ['nombre' => 'M', 'descripcion' => 'Mediana, talla más común y versátil'],
                    ['nombre' => 'L', 'descripcion' => 'Grande, para personas de complexión robusta'],
                    ['nombre' => 'XL', 'descripcion' => 'Extra grande, talla amplia y cómoda'],
                    ['nombre' => 'XXL', 'descripcion' => 'Doble extra grande, máximo confort'],
                ]
            ],
            [
                'nombre' => 'Sublimado',
                'descripcion' => 'Opciones de personalización por sublimación',
                'caracteristicas' => [
                    ['nombre' => 'Completo', 'descripcion' => 'Sublimación completa de la prenda, diseño total'],
                    ['nombre' => 'Parcial', 'descripcion' => 'Sublimación en áreas específicas de la prenda'],
                    ['nombre' => 'Solo logo', 'descripcion' => 'Únicamente logo o marca en pecho o espalda'],
                    ['nombre' => 'Frente', 'descripcion' => 'Sublimación solo en la parte frontal'],
                    ['nombre' => 'Espalda', 'descripcion' => 'Sublimación solo en la parte posterior'],
                ]
            ],
            [
                'nombre' => 'Color Base',
                'descripcion' => 'Colores base disponibles para la prenda',
                'caracteristicas' => [
                    ['nombre' => 'Blanco', 'descripcion' => 'Color blanco clásico, ideal para cualquier diseño'],
                    ['nombre' => 'Negro', 'descripcion' => 'Color negro elegante, resalta colores brillantes'],
                    ['nombre' => 'Azul', 'descripcion' => 'Azul royal, color deportivo y profesional'],
                    ['nombre' => 'Rojo', 'descripcion' => 'Rojo intenso, llamativo y energético'],
                    ['nombre' => 'Verde', 'descripcion' => 'Verde deportivo, fresco y natural'],
                    ['nombre' => 'Amarillo', 'descripcion' => 'Amarillo brillante, alegre y visible'],
                ]
            ],
            [
                'nombre' => 'Acabado',
                'descripcion' => 'Tipo de acabado y textura final',
                'caracteristicas' => [
                    ['nombre' => 'Mate', 'descripcion' => 'Acabado mate, sin brillo, elegante y sobrio'],
                    ['nombre' => 'Brillante', 'descripcion' => 'Acabado brillante, colores vivos y llamativos'],
                    ['nombre' => 'Texturizado', 'descripcion' => 'Textura especial, tacto diferenciado'],
                    ['nombre' => 'Suave', 'descripcion' => 'Acabado extra suave al tacto'],
                ]
            ],
            [
                'nombre' => 'Género',
                'descripcion' => 'Género al que está dirigida la prenda',
                'caracteristicas' => [
                    ['nombre' => 'Masculino', 'descripcion' => 'Diseño y corte para hombres'],
                    ['nombre' => 'Femenino', 'descripcion' => 'Diseño y corte para mujeres'],
                    ['nombre' => 'Unisex', 'descripcion' => 'Diseño universal para cualquier género'],
                    ['nombre' => 'Infantil', 'descripcion' => 'Especialmente diseñado para niños'],
                ]
            ]
        ];

        // Crear opciones y características
        foreach ($opciones as $opcionData) {
            echo "Creando opción: {$opcionData['nombre']}\n";
            
            $opcion = Opcion::create([
                'nombre' => $opcionData['nombre'],
                'descripcion' => $opcionData['descripcion'],
                'estado' => 1
            ]);

            foreach ($opcionData['caracteristicas'] as $caracteristicaData) {
                echo "  - Creando característica: {$caracteristicaData['nombre']}\n";
                
                Caracteristica::create([
                    'nombre' => $caracteristicaData['nombre'],
                    'descripcion' => $caracteristicaData['descripcion'],
                    'estado' => 1,
                    'idOpcion' => $opcion->idOpcion
                ]);
            }
        }

        echo "\n✅ Seeder completado exitosamente!\n";
        echo "📊 Resumen:\n";
        echo "   - Opciones creadas: " . count($opciones) . "\n";
        echo "   - Características creadas: " . array_sum(array_map(function($o) { return count($o['caracteristicas']); }, $opciones)) . "\n";
        echo "\n🎯 Próximos pasos:\n";
        echo "   1. Verificar las opciones en: /opciones\n";
        echo "   2. Crear productos y asignar variantes\n";
        echo "   3. Configurar precios adicionales por característica\n";
    }
}
