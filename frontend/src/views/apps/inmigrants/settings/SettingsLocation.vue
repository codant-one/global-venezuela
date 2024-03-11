<script setup>

import { ref } from 'vue'
import { requiredValidator } from '@validators'

const props = defineProps({
  inmigrant: {
    type: Object,
    required: false
  },
  states: {
    type: Object,
    required: true
  },
  municipalities: {
    type: Object,
    required: true
  },
  parishes: {
    type: Object,
    required: true
  },
  circuits: {
    type: Object,
    required: true
  },
  communityCouncils: {
    type: Object,
    required: true
  }
})

const emit = defineEmits([
  'submit',
  'back'
])

const listStates = ref(props.states)
const listMunicipalities = ref(props.municipalities)
const listParishes = ref(props.parishes)
const listCircuits = ref(props.circuits)
const listCommunityCouncils = ref(props.communityCouncils)

const listMunicipalitiesByStates = ref([])
const listParishesByMunicipalities = ref([])
const listCircuitsByParishes = ref([])
const listCommunityCouncilsByCircuits = ref([])

const refVForm = ref()

const state_id = ref('')
const stateOld_id = ref('')
const municipality_id = ref('')
const municipalityOld_id = ref('')
const parish_id = ref('')
const parishOld_id = ref('')
const circuit_id = ref('')
const circuitOld_id = ref('')
const community_council_id = ref('')

const locationDetail = ref({
  parish_id: '',
  community_council_id: null,
  address: ''
})

watchEffect(fetchData)

async function fetchData() {

  if(props.inmigrant) {
    locationDetail.value.parish_id = props.inmigrant.parish_id
    locationDetail.value.community_council_id = props.inmigrant.community_council.name
    locationDetail.value.address = props.inmigrant.address


    stateOld_id.value = props.inmigrant.parish.municipality.state.id
    state_id.value = props.inmigrant.parish.municipality.state.name

    municipalityOld_id.value = props.inmigrant.parish.municipality.id
    municipality_id.value = props.inmigrant.parish.municipality.name

    parishOld_id.value = props.inmigrant.parish.id
    parish_id.value = props.inmigrant.parish.name
    
    circuitOld_id.value = props.inmigrant.community_council.circuit.id
    circuit_id.value = props.inmigrant.community_council.circuit.name

    community_council_id.value = props.inmigrant.community_council_id
  }
}

const getMunicipalities = computed(() => {
  return listMunicipalitiesByStates.value.map((state) => {
    return {
      title: state.name,
      value: state.id,
    }
  })
})

const getParishes = computed(() => {
  return listParishesByMunicipalities.value.map((municipality) => {
    return {
      title: municipality.name,
      value: municipality.id,
    }
  })
})

const getCircuits = computed(() => {
  return listCircuitsByParishes.value.map((parish) => {
    return {
      title: parish.name,
      value: parish.id,
    }
  })
})

const getCommunityCouncils = computed(() => {
  return listCommunityCouncilsByCircuits.value.map((circuit) => {
    return {
      title: circuit.name,
      value: circuit.id,
    }
  })
})

const selectState = state => {
  if (state) {
    let _state = listStates.value.find(item => item.name === state)
    state_id.value = _state.name
 
    municipality_id.value = ''

    listMunicipalitiesByStates.value = listMunicipalities.value.filter(item => item.state_id === _state.id)
  }
}

const selectMunicipalities = municipality => {
  if (municipality) {
    let _municipality = listMunicipalities.value.find(item => item.id === municipality)
    municipality_id.value = _municipality.name
 
    locationDetail.value.parish_id = ''

    listParishesByMunicipalities.value = listParishes.value.filter(item => item.municipality_id === _municipality.id)

  }
}

const selectParishes = parish => {
  if (parish) {
    let _parish = listParishes.value.find(item => item.id === parish)
    parish_id.value = _parish.name
    locationDetail.value.parish_id = _parish.id

    circuit_id.value = ''
    listCircuitsByParishes.value = listCircuits.value.filter(item => item.parish_id === _parish.id)

  }
}

const selectCircuit = circuit => {
  if (circuit) {
    let _circuit = listCircuits.value.find(item => item.id === circuit)
    circuit_id.value = _circuit.name
 
    locationDetail.value.community_council_id = ''
    listCommunityCouncilsByCircuits.value = listCommunityCouncils.value.filter(item => item.circuit_id === _circuit.id)

  }
}

const onSubmit = () => {
  refVForm.value?.validate().then(({ valid: isValid }) => {
    if (isValid) {
      emit('submit', locationDetail.value)
    }
  })
}

</script>

<template>
  <VForm
    ref="refVForm"
    @submit.prevent="onSubmit"
    >
    <VCard
      title="Ubicación"
      class="mb-6"
    >
      <VCardText>
        <VRow>
          <VCol cols="12" md="6">
            <VAutocomplete
              v-model="state_id"
              label="Estado"
              :rules="[requiredValidator]"
              :items="listStates"
              item-title="name"
              item-value="name"
              :menu-props="{ maxHeight: '200px' }"
              @update:model-value="selectState"
            />
          </VCol>
          <VCol cols="12" md="6">
            <VAutocomplete
              v-model="municipality_id"
              label="Municipio"
              :rules="[requiredValidator]"
              :items="getMunicipalities"
              :menu-props="{ maxHeight: '200px' }"
              @update:model-value="selectMunicipalities"
            />
          </VCol>
          <VCol cols="12" md="6">
            <VAutocomplete
              v-model="parish_id"
              label="Parroquia"
              :rules="[requiredValidator]"
              :items="getParishes"
              :menu-props="{ maxHeight: '200px' }"
              @update:model-value="selectParishes"
            />
          </VCol>
          <VCol cols="12" md="6">
            <VAutocomplete
              v-model="circuit_id"
              label="Circuito"
              :items="getCircuits"
              :menu-props="{ maxHeight: '200px' }"
              @update:model-value="selectCircuit"
            />
          </VCol>
          <VCol cols="12" md="6">
            <VAutocomplete
              v-model="locationDetail.community_council_id"
              label="Consejo Comunal"
              :items="getCommunityCouncils"
              :menu-props="{ maxHeight: '200px' }"
            />
          </VCol>
          <VCol cols="12" md="12">
            <VTextarea
              v-model="locationDetail.address"
              rows="3"
              label="Dirección"
              :rules="[requiredValidator]"
            />
          </VCol>
        </VRow>
      </VCardText>
    </VCard>

    <div class="d-flex justify-end gap-x-4 mt-6">
      <VBtn
        color="secondary"
        variant="tonal"
        @click="emit('back')"
      >
        Atrás
      </VBtn>
      <VBtn type="submit">Siguiente</VBtn>
    </div>
  </VForm>
</template>
