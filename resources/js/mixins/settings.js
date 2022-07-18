export const settings = {
    data() {
        return {
            settings: {},
            langs: [],
            lang: '',
            langsubs: [],
            langsub: '',
            langdownload: '',
            substitles_langs: [],
        };
    },
    async mounted() {
        let response = await axios.get(url + '/admin/settings/data');
        this.settings = response.data;

        response = await http.get('https://api.themoviedb.org/3/configuration/languages?api_key=' + this.settings.tmdb_api_key);
        this.langs = response.data;

        this.langs.push({iso_639_1: 'pt-br', english_name: 'Portuguese (Brazil)' , iso_639_1: 'pt-br'});

        response = await http.get('https://api.themoviedb.org/3/configuration/languages?api_key=' + this.settings.tmdb_api_key);
        this.langsubs = response.data;
    },
};
