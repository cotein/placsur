export const getPaymentTypes = async({commit}, token) =>  {
    try {
        //window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
        
        let response = await axios.get('/api/pay_methods/index');

        commit('SET_PAYMENT_TYPES', response.data);

        return response

    } catch (e) {
        console.log('error en getAddressType action');
        console.log(e);
        throw e;
    }
}

export const updatePaymentTypeValue = ({commit}, value) => {
    commit('SET_PAYMENT_TYPE', value);
}