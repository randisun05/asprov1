<template>

    <Head>
        <title>{{ title }}</title>

    </Head>

 <!--page Header-->
<section class="page-header parallaxie padding_top center-block">
   <div class="container">
      <div class="row">
         <div class="col-sm-12">
            <div class="page-titles text-center">
               <h2 class="whitecolor font-light bottom30">{{ title }}</h2>
               <ul class="breadcrumb justify-content-center">
                 <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                 <li class="breadcrumb-item active" aria-current="page">{{ title }}</li>
               </ul>
            </div>
         </div>
      </div>
   </div>
</section>
<!--page Header ends-->


<!-- Our Team-->
<section id="our-team-three">
   <div class="container">
      <div class="row">
           <div class="col-md-4">
              <div class="team-box top60">
               <div class="image">
                  <!-- <img src="/assets/images/ritakardinasari.png" alt=""> -->
                  <img v-if="data.image" :src="getImageUrl(data.image)" alt="Gambar" />
               </div>
               <div class="team-content gradient_bg whitecolor">
                  <h3>Dr. Rita Kardinasari, S.Psi, M.Si, Dipl. TAA
                  </h3>
                  <p class="bottom40"></p>
                  <div class="progress-bars">
                     <div class="progress">
                        <div ><span></span></div>
                     </div>
                     <div class="progress">
                        <div ><span></span></div>
                     </div>
                  </div>
               </div>
            </div>
           </div>
           <div class="col-md-6 w-80">
              <div class="top60">
               <div class="heading-title text-md-start text-center padding_bottom">
               <div style="text-align: justify" >
                <div class="h5 darkcolor" v-html="data.body"></div>
             </div>

               </div>
            </div>
           </div>
      </div>
   </div>
</section>


</template>

<script>
    //import layout
    import LayoutWebsite from '../../../../Layouts/Website.vue';


    //import reactive
    import {
        reactive
    } from 'vue'

     //import Head from Inertia
     import {
        Head
    } from '@inertiajs/vue3';

    //import inertia adapter
    import { router } from '@inertiajs/vue3';

    export default {

        //layout
        layout: LayoutWebsite,

        //register component
        components: {
            Head,

        },

        //props
        props: {
            title:Object,
            data: Object,

        },

          //inisialisasi composition API
            setup() {


            // Method to get the URL of the document
            const getImageUrl = (imageName) => {
                return `/storage/${imageName}`;
            }


            //return
            return {
                getImageUrl,
            }
        }



    }

</script>
