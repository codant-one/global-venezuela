<script setup>

import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'
import logoSlogan from '@images/logo_slogan.png'
import { useAuthStores } from '@/stores/useAuth'
import miscMaskDark from '@images/pages/misc-mask-dark.png'
import miscMaskLight from '@images/pages/misc-mask-light.png'
import miscUnderMaintenance from '@images/pages/misc-under-maintenance.png'
import router from '@/router'

const authThemeMask = useGenerateImageVariant(miscMaskLight, miscMaskDark)

const route = useRoute()
const authStores = useAuthStores()

const advisor = ref({
  type: '',
  message: '',
  show: false
})

const isRequestOngoing = ref(true)

watchEffect(fetchData)

async function fetchData() {

    isRequestOngoing.value = true

    authStores.findToken(route.query.token)
        .then(response => {

            advisor.value.show = true
            advisor.value.type = 'success'
            advisor.value.message = response.message

            setTimeout(() => { 
                completed(response.data.token)
            }, 3000)

        }).catch(err => {
            advisor.value.show = true
            advisor.value.type = 'error'
            isRequestOngoing.value = false

            if(err.response.data.feedback === 'not_found' || err.response.data.feedback === 'error_token' )
                advisor.value.message = err.response.data.message
            else
                advisor.value.message = err.response.data.exception

            console.error(err.response)
        })
}

const completed = (token) => {

    authStores.completed({token: token})
        .then(response => {

            isRequestOngoing.value = false

            advisor.value.show = true
            advisor.value.type = 'success'
            advisor.value.message = response.message

            setTimeout(() => { 
                advisor.value.show = false
                advisor.value.type = ''
                advisor.value.message = ''
                router.push({ name: 'login' })
            }, 3000)
            

        }).catch(err => {
            advisor.value.show = true
            advisor.value.type = 'error'
            isRequestOngoing.value = false

            if(err.response.data.feedback === 'not_found') {
                advisor.value.message = err.response.data.message
            } else {
                advisor.value.message = err.response.data.exception
            }

            console.error(err.response)
        })
}

</script>

<template>
    <div class="misc-wrapper">

        <VDialog
            v-model="isRequestOngoing"
            width="300"
            persistent>
            
            <VCard
            color="primary"
            width="300">
                
            <VCardText class="pt-3">
                Cargando

                <VProgressLinear
                    indeterminate
                    color="white"
                    class="mb-0"/>
            </VCardText>
            </VCard>
        </VDialog>

        <v-col cols="12">
            <v-alert
            v-if="advisor.show"
            :type="advisor.type"
            class="mb-6">
                {{ advisor.message }}
            </v-alert>
        </v-col>

        <div class="text-center mb-12">
            <div class="d-flex">
                <VImg
                    :src="logoSlogan"
                    width="350"
                />
            </div>
            <VBtn to="/">
                LOGIN
            </VBtn>
        </div>

        <!-- 👉 Image -->
        <div class="misc-avatar w-100 text-center">
        <VImg
            :src="miscUnderMaintenance"
            alt="Coming Soon"
            :max-width="550"
            class="mx-auto"
        />
        </div>

        <VImg
        :src="authThemeMask"
        class="misc-footer-img d-none d-md-block"
        />
  </div>
</template>

<style lang="scss">
    @use "@core/scss/template/pages/misc.scss";
</style>

<route lang="yaml">
    meta:
      layout: blank
      action: ver
      subject: Auth
      redirectIfLoggedIn: false
</route>