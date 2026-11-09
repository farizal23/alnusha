<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $snackImages = [
            'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=900&q=85',
            'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=900&q=85',
            'https://images.unsplash.com/photo-1541592106381-b31e9677c0e5?auto=format&fit=crop&w=900&q=85',
            'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=900&q=85',
        ];
        $frozenImages = [
            'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=900&q=85',
            'https://images.unsplash.com/photo-1625220194771-7ebdea0b70b9?auto=format&fit=crop&w=900&q=85',
            'https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=900&q=85',
            'https://images.unsplash.com/photo-1563805042-7684c019e1cb?auto=format&fit=crop&w=900&q=85',
        ];
        $products = [
            ['Kriwil pedas', 73000, 'snack', '1 ball', 22, 'Camilan kriwil renyah dengan sensasi pedas yang bikin nagih.'],
            ['Kacang Thailand', 156000, 'snack', '1 ball', 18, 'Kacang gurih renyah, cocok untuk teman santai dan stok toko.'],
            ['Soes Micky', 127000, 'snack', '1 ball', 20, 'Kudapan soes favorit dengan tekstur renyah dan rasa yang ringan.'],
            ['Poken stik', 87000, 'snack', '1 ball', 18, 'Stik camilan gurih untuk teman kumpul dan suguhan keluarga.'],
            ['Pilus sarinah', 48000, 'snack', '1 ball', 28, 'Pilus renyah dengan rasa gurih yang pas untuk camilan harian.'],
            ['Telur gabus', 48000, 'snack', '1 ball', 24, 'Telur gabus klasik yang renyah, gurih, dan praktis disajikan.'],
            ['Lewo ori/pedas', 60000, 'snack', '1 ball', 20, 'Camilan lewo pilihan, tersedia dalam rasa original dan pedas.'],
            ['Basreng', 74000, 'snack', '1 ball', 22, 'Bakso goreng renyah berbumbu untuk pecinta camilan gurih.'],
            ['Seblak campur', 82000, 'snack', '1 ball', 18, 'Racikan seblak campur yang praktis dengan cita rasa khas.'],
            ['Makaroni bantet pedas daun jeruk/jagung manis', 57000, 'snack', '1 ball', 20, 'Makaroni bantet dengan pilihan pedas daun jeruk atau jagung manis.'],
            ['Keripik pisang mutiara', 62000, 'snack', '1 ball', 22, 'Keripik pisang tipis dan renyah, pas untuk teman minum teh.'],
            ['Tahu baso', 25000, 'frozen', 'per pack', 30, 'Tahu baso siap olah untuk camilan atau lauk praktis di rumah.'],
            ['Sosis Dehotel', 33000, 'frozen', 'per pack', 26, 'Sosis pilihan yang praktis disajikan untuk berbagai menu.'],
            ['Nugget Champ richeese', 38000, 'frozen', '400–500 gr', 24, 'Nugget ayam dengan balutan rasa keju yang disukai keluarga.'],
            ['Kentang', 38000, 'frozen', '1 kg', 28, 'Kentang beku praktis, siap digoreng untuk camilan favorit.'],
            ['Mini pao', 20000, 'frozen', 'per pack', 24, 'Pao mungil yang lembut dan mudah disajikan kapan saja.'],
            ['Dori filet', 50000, 'frozen', '1 kg', 18, 'Filet dori beku yang praktis untuk aneka masakan rumahan.'],
            ['Siomay ayam', 36000, 'frozen', '1 pack isi 10 pcs', 24, 'Siomay ayam lezat, praktis disajikan untuk keluarga.'],
            ['Siomay ayam jamur', 36000, 'frozen', 'per pack', 20, 'Paduan ayam dan jamur dalam siomay lembut penuh rasa.'],
            ['Siomay mercon', 36000, 'frozen', 'per pack', 18, 'Siomay dengan isian pedas untuk penikmat rasa berani.'],
            ['Siomay moza', 36000, 'frozen', 'per pack', 20, 'Siomay dengan sentuhan keju mozzarella yang gurih.'],
            ['Siomay nori', 38000, 'frozen', 'per pack', 18, 'Siomay ayam dibalut nori, nikmat untuk camilan atau lauk.'],
            ['Lumpia kulit tahu', 38000, 'frozen', 'per pack', 20, 'Lumpia kulit tahu gurih yang praktis digoreng saat dibutuhkan.'],
            ['Ekado', 38000, 'frozen', 'per pack', 18, 'Ekado gurih untuk melengkapi menu dan camilan keluarga.'],
            ['Kwotie', 36000, 'frozen', 'per pack', 18, 'Kwotie praktis dengan rasa gurih untuk camilan hangat.'],
        ];

        foreach ($products as $index => [$name, $price, $category, $unit, $stock, $description]) {
            Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'category' => $category,
                    'price' => $price,
                    'description' => $description,
                    'unit' => $unit,
                    'stock' => $stock,
                    'is_active' => true,
                    'is_featured' => $index < 6,
                    'image_url' => ($category === 'snack' ? $snackImages : $frozenImages)[$index % 4],
                ],
            );
        }
    }
}
