# Artroplasti WordPress Teması

## Açıklama
Kalça Diz Artroplasti Derneği için özel olarak geliştirilmiş WordPress teması.

## Özellikler
- Özel post type'lar (Haberler, Kurslar, Kongreler, Webinarlar)
- Kullanıcı üyelik sistemi
- Ödeme geçmişi takibi
- Responsive tasarım
- Çoklu dil desteği hazır

## Kurulum
1. Temayı wp-content/themes/ klasörüne yükleyin
2. WordPress Yönetim Paneli > Görünüm > Temalar'dan "Artroplasti" temasını aktifleştirin
3. Gerekli sayfaları oluşturun (Giriş Yap, Hesabım vb.)

## Gerekli Sayfalar
- Anasayfa (front-page.php kullanır)
- Giriş Yap (templates/login.php şablonunu kullanın)
- Hesabım (templates/account.php şablonunu kullanın)

## Konfigürasyon
Tema, özel veritabanı tablosu oluşturur:
- wp_artroplasti_payments (ödeme geçmişi için)

## Geliştiriciler
- Custom post types: inc/custom-post-types.php
- User functions: inc/user-functions.php
- Payment functions: inc/payment-functions.php

## Kullanım
HTML içeriklerinizi ilgili template dosyalarına ekleyin:
- header.php - Site başlığı ve menü
- footer.php - Site alt bilgi
- front-page.php - Anasayfa içeriği
- templates/login.php - Giriş sayfası
- templates/account.php - Kullanıcı hesap sayfası

## CSS ve JavaScript
- assets/css/custom.css - Özel stiller
- assets/js/custom.js - Özel JavaScript kodları

## Sürüm
1.0.0

## Lisans
GNU General Public License v2 or later
