<template>
  <div>
    <table class="table table-hover">
      <thead>
        <tr>
          <th v-for="(t, key) in titulos" :key="key">
            {{ t.titulo }}
          </th>
          <th
            v-if="visualizar.visivel || atualizar.visivel || remover.visivel"
          ></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(obj, chave) in dadosFiltrados" :key="chave">
          <td v-for="(valor, chaveValor) in obj" :key="chaveValor">
            <span v-if="titulos[chaveValor].tipo == 'texto'">{{ valor }}</span>
            <span v-if="titulos[chaveValor].tipo == 'data'">{{
              valor | formataDataTempoGlobal
            }}</span>
            <span v-if="titulos[chaveValor].tipo == 'imagem'">
              <img :src="'/storage/' + valor" alt="" width="30" height="30" />
            </span>
          </td>
          <td v-if="visualizar.visivel || atualizar.visivel || remover.visivel">
            <button
              v-if="visualizar.visivel"
              class="btn btn-outline-primary btn-sm"
              :data-toggle="visualizar.dataToggle"
              :data-target="visualizar.dataTarget"
              @click="setStore(obj)"
            >
              Visualizar
            </button>
            <button
              v-if="atualizar.visivel"
              class="btn btn-outline-success btn-sm"
              :data-toggle="atualizar.dataToggle"
              :data-target="atualizar.dataTarget"
              @click="setStore(obj)"
            >
              Atualizar
            </button>
            <button
              v-if="remover.visivel"
              class="btn btn-outline-danger btn-sm"
              :data-toggle="remover.dataToggle"
              :data-target="remover.dataTarget"
              @click="setStore(obj)"
            >
              Remover
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  props: ["dados", "titulos", "visualizar", "atualizar", "remover"],
  methods: {
    setStore(obj) {
      this.$store.state.transacao.status = "";
      this.$store.state.transacao.mensagem = "";
      this.$store.state.transacao.dados = "";
      this.$store.state.item = obj;
      console.log(obj);
    },
  },
  computed: {
    dadosFiltrados() {
      let campos = Object.keys(this.titulos);
      let dadosFiltrados = [];

      if (Array.isArray(this.dados)) {
        this.dados.forEach((item, chave) => {
          let itemFiltrado = {};

          campos.forEach((campo) => {
            // Usando a sintaxe de array para atribuir valores ao objeto itemFiltrado
            itemFiltrado[campo] = item[campo]; //podemos utilizar a sintaxe de array para atribuir valores a objetos
          });

          dadosFiltrados.push(itemFiltrado);
        });
      }

      return dadosFiltrados;
    },
  },
};
</script>
