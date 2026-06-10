# GymBro
GymBro QR Tabanlı Spor ve Beslenme Takip Sistemi

                              GYMBRO QR TABANLI SPOR VE BESLENME TAKİP SİSTEMİ

      Proje Açıklaması

Biz bir spor salonu için web sitesi yapmak istedik. Öncelikle bir Admin girişi gerekiyor. Admin dediğimiz kişi her şeye erişimi olan tek kişidir (Spor salonunun sahibi veya yöneticisi olabilir). Üye ve antrenör ekleme kısımları admin sayfasına has olan özelliklerdir. Ayrıca üyelere bir antrenör atar. Böylece herkesin kendine özel bir antrenörü olur.
Antrenör sayfasında; üye için yemek listesi, antrenman programı ve not oluşturabilir. Kendi üyelerinin girdiği su bilgilerine de ulaşabilir.
Üye sayfasında üyeler sadece su girişi yapabilirler. Onun dışında antrenörlerinin onlar için gönderdiği yemek listelerine, antrenman programlarına ve notlarına ulaşabilirler. 
Her üyenin kendine özel QR kodu vardır. Bu kodla birlikte spor salonuna QR kodu okutarak giriş yapabilirler.
      
      Kullanıcı Rolleri

Admin = Sistemi yöneten kişi. Önceden veritabanına kaydedilmiş durumdadır. Antrenör ve üyeleri ekleyip silebilir.
Antrenör = Sadece admin tarafından eklenebilir. Kendi üyelerine antrenman programı, yemek listesi ve not yazabilir. Admin onun için önce giriş şifresi atar ve giriş yapmak istediğinde bu verilen şifreyi değiştirmek zorundadır.
Üye = Admin tarafından belirli bir antrenöre atanabilir. Giriş yaparak antrenörün ona hazırladığı antrenman ve yemek listelerini görebilir. Su takibi yapabilir. Admin onun için önce giriş şifresi atar ve giriş yapmak istediğinde bu verilen şifreyi değiştirmek zorundadır.

      Kullanılan Diller

phpMyAdmin Veritabanı, HTML/CSS ve PHP kullanılmıştır.

      Dosya Yapısı

db.php               => Veritabanı bağlantısı kuruldu. 

role_control.php     => Role göre erişim.

sign_in.php          => Giriş sayfası.

login.php            => Sign_in.php dosyasına aktarıyor. (Başka bir düşüncemiz vardı fakat çok fazla hata aldığımız ve bütün dosyalara tekrardan bakıp düzeltme yapacak vaktimiz olmadığı için böyle bir çözüm bulduk.)

logout.php           => Çıkış yapıyor ve sign_in sayfasına aktarıyor.

setup.php            => İlk admin kurulumu

change_password.php  => Şifre değiştirmeye zorladığımız ve şifrelerin hashlendiği dosya.

navbar.php           => Sayfalar arası geçiş ve rol kontrolü yaparak role göre içerik sunar.

member_view.php      => Üye ekranı.

add_member.php       => Üye ekleme 

list_member.php      => Üye listesini görme

edit_member.php      => Üye düzenle

delete_member.php    => Üye silme

add_trainer.php      => Antrenör ekleme

list_trainers.php    => Antrenör listesi

edit_trainer.php     => Antrenör düzenleme
delete_trainer.php   => Antrenör silme
add_program.php      => Antrenman programı ekleme
list_program.php     => Program listesi
edit_program.php     => Program düzenleme
delete_program.php   => Program silme
add_exercise.php     => Egzersiz ekleme
list_exercise.php    => Egzersiz listesi
edit_exercise.php    => Egzersiz düzenleme
delete_exercise.php  => Egzersiz silme
add_meal.php         => Yemek ekleme
list_meal.php        => Yemek listesi
edit_meal.php        => Yemek düzenleme
delete_meal.php      => Yemek silme
add_water.php        => İçilen su miktarını ekleme
list_water.php       => Su takibi listesi
delete_water.php     => Su kaydı silme
add_note.php         => Antrenör ve adminin not ekleme ekranı
list_notes.php       => Not listesi
edit_note.php        => Not düzenleme
delete_note.php      => Not silme
ekran görüntüleri    =>
<img width="1915" height="903" alt="Ekran görüntüsü 2026-06-10 230030" src="https://github.com/user-attachments/assets/53b65552-fc92-4546-bff2-ee353244b647" />
<img width="1918" height="911" alt="Ekran görüntüsü 2026-06-10 230019" src="https://github.com/user-attachments/assets/d4fc94c7-5e3b-4471-b04d-aa41eaf307c3" />
<img width="1918" height="910" alt="Ekran görüntüsü 2026-06-10 230011" src="https://github.com/user-attachments/assets/8d5e2c9d-ab6c-4cd0-a5da-d17480cd7b13" />
<img width="1918" height="906" alt="Ekran görüntüsü 2026-06-10 230000" src="https://github.com/user-attachments/assets/e514a133-82fd-478a-968f-68426ddbb49c" />
<img width="1918" height="902" alt="Ekran görüntüsü 2026-06-10 225947" src="https://github.com/user-attachments/assets/c5c4c0f7-b3ed-4b8f-a628-6c82e4e5e1f4" />
<img width="1918" height="910" alt="Ekran görüntüsü 2026-06-10 225932" src="https://github.com/user-attachments/assets/02894fe5-3d8e-4b7b-bab3-00b0b9ee28da" />
<img width="1918" height="911" alt="Ekran görüntüsü 2026-06-10 225924" src="https://github.com/user-attachments/assets/b5423bdd-bb60-44e4-ab00-949b98520a6f" />
<img width="1907" height="971" alt="Ekran görüntüsü 2026-06-10 225902" src="https://github.com/user-attachments/assets/469ea5ae-dbd0-4765-b39d-6aeaba70e79c" />

