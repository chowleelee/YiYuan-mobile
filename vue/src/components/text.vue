 <template >
  <div class="text">
    <div class="answer-text-content">
      <p class="answer-content" ref="mycontent">
        {{format_content}}
        <span class="answer-show" @click="seeAll()" v-if="showAll && isLong">全文</span>
        <span class="answer-show" @click="hide()" v-if="showAll && !isLong">收起</span>
      </p>
    </div>
  </div>  
 </template>

 <script type="es6">
 
export default {
  props:['content','flag'],
  data(){
    return {
      showAll: false,
      answer_max_length:20, //回答文字形式显示全文的最达长度
      isLong: true,
      format_content:''
    }
  },
  mounted(){
  },
  created(){
    if(this.content.length > this.answer_max_length){
      this.format_content = this.content.substr(0, 20) + '...';
      this.showAll = true;
    }else{
      this.format_content = this.content;
    }
  },
  methods:{
    seeAll(){
      //this.showAll = true;
      this.format_content = this.content;
      this.isLong = false;
    },
    hide(){
      this.format_content = this.content.substring(0, 20) + '...';
      this.isLong = true;
    }
  }
  /*created(){
    //if(this.flag == 2){
      //是否显示所有文字答案
      //this.showAll = false;
      if(this.content.length>this.answer_max_length){
        //文字答案是否过长
        this.isLong = true;
      }else{
        this.isLong = false;
      }
   // }
  }*/
}
</script>
<style>
@import '../assets/css/text.css';
 
</style>