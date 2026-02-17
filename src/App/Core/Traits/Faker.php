<?php 
declare(strict_types=1); 

namespace App\Core\Traits; 

trait Faker 
{ 
     public array $names = [
        'Tastiera Meccanica RGB',
        'Monitor 27" 4K',
        'Mouse Wireless Ergonomico',
        'SSD NVMe 1TB',
        'Cuffie Bluetooth con ANC',
        'Webcam Full HD',
        'Stampante Laser Wi-Fi',
        'Router Dual Band Gigabit',
        'Power Bank 20.000mAh',
        'Notebook 15" Intel i7',
        'Smartwatch GPS',
        'Speaker Bluetooth Portatile',
        'Microfono USB Condensatore',
        'Hub USB-C 7 porte',
        'Supporto Laptop Regolabile'
     ], 

     $prices = [
        89.99,
        329.00,
        49.50,
        119.90,
        149.00,
        59.99,
        139.00,
        89.00,
        39.90,
        899.00,
        199.00,
        79.00,
        109.00,
        34.99,
        44.90
     ],
     
     $categories = [
        'Periferiche',
        'Display',
        'Periferiche',
        'Storage',
        'Audio',
        'Video',
        'Stampa',
        'Rete',
        'Energia',
        'Computer',
        'Wearable',
        'Audio',
        'Accessori',
        'Accessori',
        'Accessori'
     ], 
     
     $descriptions = [
        'Tastiera con switch blu, retroilluminazione RGB e layout italiano.',
        'Monitor UHD con pannello IPS e supporto HDR10.',
        'Mouse con impugnatura verticale e batteria ricaricabile.',
        'Unità SSD ad alte prestazioni con interfaccia PCIe Gen4.',
        'Cuffie over-ear con cancellazione attiva del rumore e autonomia di 30 ore.',
        'Webcam con microfono integrato e risoluzione 1080p.',
        'Stampante laser monocromatica con connettività wireless.',
        'Router Wi-Fi 6 con porte Gigabit e copertura estesa.',
        'Power bank ad alta capacità con ricarica rapida USB-C.',
        'Notebook con 16GB RAM e SSD da 512GB.',
        'Smartwatch con cardiofrequenzimetro e tracciamento attività.',
        'Speaker compatto con bassi potenziati e autonomia di 12 ore.',
        'Microfono USB ideale per podcast e streaming.',
        'Hub USB-C con HDMI, Ethernet e lettore SD.',
        'Supporto in alluminio regolabile per laptop fino a 17".'
     ],
     
     $availables = [
        true, false
     ],
     
     $brands = [
        'Logitech', 'LG', 'Trust', 'Samsung', 'Sony',
        'Microsoft', 'HP', 'TP-Link', 'Anker', 'Dell',
        'Garmin', 'JBL', 'Blue', 'Ugreen', 'Nulaxy'
     ],
     
     $codes = [
        'LOG-RGB-001', 'LG-4K-027', 'TR-MOUSE-ERG', 'SAM-SSD-1TB', 'SONY-ANC-BT',
        'MS-WEB-HD', 'HP-LASER-WF', 'TPL-ROUTER-DB', 'ANK-PB-20K', 'DEL-NB-I7',
        'GAR-GPS-WATCH', 'JBL-SPK-BT', 'BLUE-MIC-USB', 'UGR-HUB-7P', 'NUL-LAP-STD'
     ], 

     $name_images = [
        'image1',
        'image2',
        'image3',
        'image4', 
        'image5',
        'image6'
     ],
     
     $weights = [ 
        0.9, 4.5, 0.2, 0.05, 0.3,
        0.15, 6.0, 0.8, 0.4, 2.2,
        0.1, 0.6, 0.7, 0.3, 1.1
    ];

    public function randomValues(array $array) : mixed 
    {
        $key = array_rand($array); 
        return $array[$key]; 
    }
    
}