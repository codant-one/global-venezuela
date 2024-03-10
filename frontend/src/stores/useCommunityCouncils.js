import { defineStore } from 'pinia'
import CommunityCouncils from '@/api/community-councils'

export const useCommunityCouncilsStores = defineStore('community-councils', {
    state: () => ({
        communityCouncils: {},
        loading: false,
        last_page: 1,
        communityCouncilsTotalCount: 6
    }),
    getters:{
        getCommunityCouncils(){
            return this.communityCouncils
        }
    },
    actions: {
        setLoading(payload){
            this.loading = payload
        },
        fetchCommunityCouncils(params) {
            this.setLoading(true)
            
            return CommunityCouncils.get(params)
                .then((response) => {
                    this.communityCouncils = response.data.data.communityCouncils.data
                    this.last_page = response.data.data.communityCouncils.last_page
                    this.communityCouncilsTotalCount = response.data.data.communityCouncilsTotalCount
                })
                .catch(error => console.log(error))
                .finally(() => {
                    this.setLoading(false)
                })
            
        },
        addCommunityCouncil(data) {
            this.setLoading(true)

            return CommunityCouncils.create(data)
                .then((response) => {
                    this.communityCouncils.push(response.data.data.communityCouncil)
                    return Promise.resolve(response)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })
            
        },
        showCommunityCouncil(id) {
            this.setLoading(true)

            return CommunityCouncils.show(id)
                .then((response) => {
                    if(response.data.success)
                        return Promise.resolve(response.data.data.communityCouncil)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })
            
        },
        updateCommunityCouncil(data, id) {
            this.setLoading(true)
            
            return CommunityCouncils.update(data, id)
                .then((response) => {
                    let pos = this.communityCouncils.findIndex((item) => item.id === response.data.data.communityCouncil.id)
                    this.communityCouncils[pos] = response.data.data.communityCouncil
                    return Promise.resolve(response)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })
         
        },
        deleteCommunityCouncil(id) {
            this.setLoading(true)

            return CommunityCouncils.delete(id)
                .then((response) => {
                    let index = this.communityCouncils.findIndex((item) => item.id === id)
                    this.communityCouncils.splice(index, 1)
                    return Promise.resolve(response)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })  
        }
    }
})
