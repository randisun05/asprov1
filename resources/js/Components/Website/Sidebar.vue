<template>
    
    <!-- header -->
    <header class="site-header transparent-sidemenu">  
        <!-- side menu -->
        <div class="side-menu">
           <div class="inner-wrapper">
              <span class="btn-close" id="btn_sideNavClose"><i></i><i></i></span>
              <nav class="side-nav">
                 <ul class="navbar-nav w-100">
                    <li v-for="(dropdown, name) in dropdowns" :key="name" class="nav-item active">
                 <a class="nav-link pagescroll" :href="`#${name}`" @click="toggleDropdown(name)">{{ name }}</a>
                 <div v-if="dropdown" class="dropdown-content">
                   <div v-for="item in dropdownItems[name]" :key="item">
                     <a :href="`#${item.toLowerCase().replace(/\s+/g, '-')}`">{{ item }}</a>
                   </div>
                 </div>
               </li>
                 </ul>
              </nav>
     
              <div class="side-footer w-100">
                 <ul class="social-icons-simple white top40">
                 <li><a href="javascript:void(0)"><i class="fa fa-facebook"></i> </a> </li>
                 <li><a href="javascript:void(0)"><i class="fa fa-instagram"></i> </a> </li>
                 <li><a href="javascript:void(0)"><i class="fa fa-twitter"></i> </a> </li>
              </ul>
              <p class="whitecolor">&copy; 2024 Aspro</p>
              </div>
           </div>
        </div>
        <a id="close_side_menu" href="javascript:void(0);"></a>
        <!-- End side menu -->
     </header>
     <!-- header -->
    </template>
    
    <script>
    
        import {
            Head,
            Link
        } from '@inertiajs/vue3';
    
        //import ref from vue
        import {
            ref
        } from 'vue';
    
        //import inertia adapter
        import { router } from '@inertiajs/vue3';
    
    
        export default {
          data() {
             return {
                dropdowns: {
                   home: false,
                   profile: false,
                   program: false,
                   publikasi: false,
                   keanggotaan: false,
                   kontak: false,
                    // Tambahkan dropdown lainnya di sini
                            },
                      dropdownItems: {
                   profile: ["Tentang Aspro", "Ketua Umum", "Struktur Organisasi", "Visi Misi", "Sejarah Terbentuknya Aspro", "Peraturan Organisasi"],
                   program: ["Pelatihan", "Sertifikasi", "Kegiatan Aspro"],
                   publikasi: ["Pengumuman", "Artikel", "Berita", "Media"],
                   keanggotaan: ["Login", "Registrasi"],
                   // Tambahkan item dropdown lainnya di sini
                                     }
                   };
                },
                   methods: {
                   toggleDropdown(dropdown) {
                   this.dropdowns[dropdown] = !this.dropdowns[dropdown];
                                           }
                            }
        }
    
    </script>
    
    <style>
    
    </style>