const actions = {

    async getVouchers(_){

        try {

            const response = await axios.get('/api/vouchers/index');

            return response.data;

        } catch (error) {
            
            throw error;
        }
    }
}

export default actions;