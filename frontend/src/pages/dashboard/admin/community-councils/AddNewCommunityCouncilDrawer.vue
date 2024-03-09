<script setup>
import { themeConfig } from '@themeConfig'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { requiredValidator } from '@validators'
import { avatarText } from '@/@core/utils/formatters'
import { usePasswordsStores } from '@/views/dashboard/passwords/usePasswords'
import { useConfigsStores } from '@/views/dashboard/configs/useConfigs'

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
  password: {
    type: Object,
    required: false,
  }

})

const emit = defineEmits([
  'update:isDrawerOpen',
  'passwordData',
])

const passwordsStores = usePasswordsStores()
const configsStores = useConfigsStores()

const isFormValid = ref(false)
const refForm = ref()

const id = ref(0)
const name = ref('')
const user = ref('')
const logo = ref('')
const password = ref('')
const avatar = ref('')
const filename = ref([])
const isEdit = ref(false)
const masterPassword = ref('')
const setting = ref([])

const getTitle = computed(() => {
  return isEdit.value ? 'Actualizar Contraseña': 'Agregar Contraseña'
})

watchEffect(async() => {
  if (props.isDrawerOpen) {
    if (!(Object.entries(props.password).length === 0) && props.password.constructor === Object) {

      await configsStores.getFeature('setting')
      setting.value = configsStores.getFeaturedConfig('setting')

      masterPassword.value = setting.value.master_password

      let data = {
        id: props.password.id,
        master_password: masterPassword.value
      }

      passwordsStores.showPassword(data)
        .then((res) => {
          password.value = res.data.message
        })
        .catch((err) => {
          advisor.value = {
            type: 'error',
            message: err,
            show: true
          }
      })

      isEdit.value = true
      id.value = props.password.id
      name.value = props.password.name
      user.value = props.password.user
      avatar.value = props.password.logo === null ? null : themeConfig.configuraciones.urlStorage + props.password.logo
    }
  }
})

// 👉 drawer close
const closeNavigationDrawer = () => {
  emit('update:isDrawerOpen', false)
  nextTick(() => {
    refForm.value?.reset()
    refForm.value?.resetValidation()

    logo.value = ''
    avatar.value = ''
    filename.value = []
    isEdit.value = false
    id.value = 0
  })
}

const onSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
      let formData = new FormData()

      formData.append('id', id.value)
      formData.append('logo', logo.value ?? null)
      formData.append('name', name.value)
      formData.append('user', user.value)
      formData.append('password', password.value)


      emit('passwordData', { data: formData, id: id.value }, isEdit.value ? 'update' : 'create')
      emit('update:isDrawerOpen', false)
      nextTick(() => {
        refForm.value?.reset()
        refForm.value?.resetValidation()
        logo.value = ''
        avatar.value = ''
        filename.value = []
        isEdit.value = false
        id.value = 0
      })
    }
  })
}

const onImageSelected = event => {
  const file = event.target.files[0]

  if (!file) return
  // logo.value = file

  URL.createObjectURL(file)

  resizeImage(file, 400, 400, 0.9)
    .then(async blob => {
      logo.value = blob
      let r = await blobToBase64(blob)
      avatar.value = 'data:image/jpeg;base64,' + r
    })
}

const resizeImage = function(file, maxWidth, maxHeight, quality) {
  return new Promise((resolve, reject) => {
    const img = new Image()

    img.src = URL.createObjectURL(file)
    img.onload = () => {
      const canvas = document.createElement('canvas')
      const ctx = canvas.getContext('2d')

      let width = img.width
      let height = img.height

      if (maxWidth && width > maxWidth) {
        height *= maxWidth / width
        width = maxWidth
      }

      if (maxHeight && height > maxHeight) {
        width *= maxHeight / height
        height = maxHeight
      }

      canvas.width = width
      canvas.height = height

      ctx.drawImage(img, 0, 0, width, height)

      canvas.toBlob(blob => {
        resolve(blob)
      }, file.type, quality)
    }
    img.onerror = error => {
      reject(error)
    }
  })
}

const blobToBase64 = blob => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()

    reader.readAsDataURL(blob)
    reader.onload = () => {
      resolve(reader.result.split(',')[1])
    }
    reader.onerror = error => {
      reject(error)
    }
  })
}

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}
</script>

<template>
  <VNavigationDrawer
    temporary
    :width="400"
    location="end"
    class="scrollable-content"
    :model-value="props.isDrawerOpen"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Title -->
    <div class="d-flex align-center pa-6 pb-1">
      <h6 class="text-h6">
        {{ getTitle }}
      </h6>

      <VSpacer />

      <!-- 👉 Close btn -->
      <VBtn
        variant="tonal"
        color="default"
        icon
        size="32"
        class="rounded"
        @click="closeNavigationDrawer"
      >
        <VIcon
          size="18"
          icon="tabler-x"
        />
      </VBTn>
    </div>

    <PerfectScrollbar :options="{ wheelPropagation: false }">
      <VCard flat>
        <VCardText>
          <v-row>
            <v-col cols="12" class="d-flex justify-center align-center">
              <VAvatar
                size="x-large"
                color="primary"
                variant="tonal"
              >
                <VImg
                  v-if="avatar !== null"
                  :src="avatar"
                />
                <span
                  v-else
                  class="text-3xl font-weight-semibold"
                >
                    {{ avatarText(user) }}
                  </span>
              </VAvatar>
            </v-col>

            <VCol cols="12">
              <VFileInput
                v-model="filename"
                label="Logo"
                class="mb-2"
                accept="image/png, image/jpeg, image/bmp"
                prepend-icon="tabler-camera"
                @change="onImageSelected"
                @click:clear="avatar = null"
              />
            </VCol>
          </v-row>

          <!-- 👉 Form -->
          <VForm
            ref="refForm"
            v-model="isFormValid"
            @submit.prevent="onSubmit"
          >
            <VRow>
              <!-- 👉 Name -->
              <VCol cols="12">
                <VTextField
                  v-model="name"
                  :rules="[requiredValidator]"
                  label="Nombre"
                />
              </VCol>

              <!-- 👉 User -->
              <VCol cols="12">
                <VTextField
                  v-model="user"
                  :rules="[requiredValidator]"
                  label="Usuario"
                />
              </VCol>

              <!-- 👉 Password -->
              <VCol cols="12">
                <VTextField
                  v-model="password"
                  type="password"
                  :rules="[requiredValidator]"
                  label="Contraseña"
                />
              </VCol>

              <!-- 👉 Submit and Cancel -->
              <VCol cols="12">
                <VBtn
                  type="submit"
                  class="me-3"
                >
                  {{ isEdit ? 'Actualizar': 'Agregar' }}
                </VBtn>
                <VBtn
                  type="reset"
                  variant="tonal"
                  color="secondary"
                  @click="closeNavigationDrawer"
                >
                  Cancelar
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </PerfectScrollbar>
  </VNavigationDrawer>
</template>
