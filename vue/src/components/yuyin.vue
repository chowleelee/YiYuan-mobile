 <template >
  <div class="myyuyin">
     <div class="bubble bubble-blue" @click="playYuyin()">
          <span class="bubble-tail"></span>
          <span class="wave3" :class="{'bubble-playing':myisPlay==index}"></span>
          <span class="bubble-tip">{{bubbleTip}}</span>
          <span class="myDuration" v-show="showDuration">{{duration}}</span>
      </div>  
      <!-- <audio src="../../static/audio/8310.mp3" ref="myAudio" @canplay="yuyinDuration"  @ended="audioEnd()">{{index}}</audio> -->
      <audio :src="src" ref="myAudio" @canplay="yuyinDuration"  @ended="audioEnd()">{{index}}</audio>
  </div>

 </template>

 <script type="es6">
 
export default {
  props:['index','src','isplay','showDuration'],
  data(){
    return {
      myisPlay:this.isplay,                   //-1：音频暂停
      bubbleTip:'点击播放',                   //语音文字提示
      duration:''                             //播放时长
    }
  },
  created(){
     
  },
  watch:{
    isplay:function(newVal){
      let audio = this.$refs.myAudio;
      if(this.myisPlay!=-1){
       if(newVal== this.index){
        audio.play();
        this.myisPlay = this.index;
        this.bubbleTip= '正在播放';
        }else{
          audio.pause();
          this.myisPlay = -1;
          this.bubbleTip= '已经暂停';
        }
      }
    }
  },
  methods:{
    //点击播放、暂停
    playYuyin:function(){
      let audio = this.$refs.myAudio;
      this.$emit('stopPlay',this.index);
      if(this.myisPlay != this.index){
        audio.play();
        this.myisPlay = this.index;
        this.bubbleTip= '正在播放';
      }else{
        audio.pause();
        this.myisPlay = -1;
        this.bubbleTip= '已经暂停';
      }
    },
    //播放完毕
    audioEnd:function(){
      let audio = this.$refs.myAudio;
      if(audio.ended){
        this.bubbleTip= '点击播放';
        this.myisPlay = -1;
      }
    },
    //语音长度
    yuyinDuration:function(){
      let audio = this.$refs.myAudio;
      this.duration = Math.ceil(audio.duration)+'\'\'';
      // console.log(audio.duration);
    }
  }
}
</script>
<style>
@import '../assets/css/yuyin.css';
 
</style>