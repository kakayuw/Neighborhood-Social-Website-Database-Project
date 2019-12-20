<template>
<el-container>
  <div class="row">
  <el-form :inline="true"  class="demo-form-inline centerform">
    <el-form-item  v-if="block">
      <h3><el-tag type="success">{{block.ltype}}</el-tag>&nbsp;{{block.lname}}</h3>
      <p><i class="el-icon-location-information"></i>{{block.address}}</p>
    </el-form-item>
    <el-form-item>
      <el-button type="primary" @click="blockNewMessage()">New Message</el-button>
    </el-form-item>
  </el-form>

  <el-form>
    <ul class="infinite-list" v-infinite-scroll="load" style="overflow:auto">
      <li v-for="m in thread.messages" :key="m.mid" class="infinite-list-item">
        <el-form-item :label="m.mtimestamp">
          <el-card  :style="'width:80%;'+ (m.replyto == null?'':'margin-left:5%;background-color:#EBEEF5;')" >
            <h4>{{m.title}}</h4>
            <p>{{m.text}}</p>
            <p><font color="#67C23A">{{m.uname}}</font> published at {{m.mtimestamp}}</p>
            <el-button type="text" size="mini" @click="replyMessage(m.mid)">reply</el-button>
          </el-card>
        </el-form-item>
      </li>
    </ul>
  </el-form>
  </div>






  <!-- DIALOG START -->
  <el-dialog :title="is_init_msg?'Select friends and neighbors to form a message group!':(init_msg.replyto?'Reply to: '+init_msg.replyto.title:'New Message')" :visible.sync="dialogFormVisible" :fullscreen='true'>
    <el-form :model="init_msg">
      <el-form-item label="Topic" :label-width="formLabelWidth" v-if="!init_msg.replyto">
        <el-input v-model="init_msg.topic" autocomplete="off" ></el-input>
      </el-form-item>
      <el-form-item label="Reply to:" :label-width="formLabelWidth" v-if="init_msg.replyto">
        <p><font color="#606266">{{init_msg.replyto.uname}}@ {{init_msg.replyto.mtimestamp}}: {{init_msg.replyto.text}}</font> </p>
      </el-form-item>
      <el-form-item label="From" :label-width="formLabelWidth" v-if="is_init_msg">
        <el-switch  style="display: block"  active-color="#67C23A"  inactive-color="#E6A23C"  active-text="my friends"  inactive-text="my neighbors"  v-model="init_msg.isfriend"></el-switch>
      </el-form-item>
      <el-form-item label="Members" :label-width="formLabelWidth" v-if="is_init_msg">
        <el-select v-model="init_msg.members" placeholder="Select group member" multiple > <!-- optimal:collapse-tags -->
          <el-option v-for="user in init_msg.isfriend?friends:neighbors" :key="user.uid" :label="user.name" :value="user.uid" ></el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="Text" :label-width="formLabelWidth">
        <el-input  type="textarea"  :rows="3"  placeholder="Say 'Hi!' to groups!"  v-model="init_msg.text"></el-input>
      </el-form-item>
      <el-form-item label="Choose Address from">
      <el-radio-group v-model="init_msg.msg_status" @change="RadioChangeHandler">
        <el-radio-button label="MapLoc" >From Map</el-radio-button>
        <el-radio-button label="CurLoc" >Current Location</el-radio-button>
        <el-radio-button label="MyLoc"  >My Location</el-radio-button>
        <el-radio-button label="None" >None</el-radio-button>
      </el-radio-group>
    </el-form-item>
    <el-form-item size="Address" label="Address of Message">
      <h4 id="address">{{init_msg.address}}</h4>
    </el-form-item>
    <!-- BEGIN MAP -->
    <el-form-item   v-if="init_msg.msg_status=='MapLoc'" label="Map" :label-width="formLabelWidth">
      <div style="height:250; width:400px;">
        <div id="mapf" style="height:250px;width:400px;"></div>
      </div>
    </el-form-item>
    <el-form-item   v-if="init_msg.msg_status=='MapLoc'" label="Lock" :label-width="formLabelWidth">
      <el-button type="primary" @click="lockMap()">lock</el-button>
    </el-form-item>
    <!-- END MAP -->
    </el-form>
    <span slot="footer" class="dialog-footer">
      <el-button @click="dialogFormVisible = false;this.init_msg.replyto = null;">Cancel</el-button>
      <el-button type="primary" @click="submitMessage()">Confirm</el-button>
    </span>
  </el-dialog>
  <!-- DIALOG END -->
</el-container>
</template>

<script>
  import JQuery from 'jquery'
  export default {
    props: [],
    data() {
      return {
        myid: -1,
        myname: '',
        friends:'',
        neighbors:'',
        myposition: null,
        search: '',
        dialogTableVisible: false,
        dialogFormVisible: false,
        is_init_msg : true,
        init_msg: {
          text: '',
          topic: '',
          members: [],
          isfriend: false,
          msg_status: 'None',
          address:'',
          lat:0,
          lng:0,
          replyto: null,
        },
        formLabelWidth:'120px',
        marker: null,
        map: null,
        currentpos: null,
        currentaddress: null,
        drawer: false,
        thread: {
          info: null,
          messages: []
        },
        block: null,
      };
    },
    // beforeMount () {

    // },
    methods: {
      // Bunton handler
      handleClose(done) {
        this.$confirm('Are you sure you want to close this?')
          .then(_ => {
            done();
            this.thread.title = '';
            this.thread.messages = '';
            this.thread.members = '';
          })
          .catch(_ => {});
      },
      handleDelete(index, row) {
        console.log(index, row);
      },
      RadioChangeHandler(status) {
        if (status == 'None') {
          this.init_msg.address = '';
        } else if (status == 'MapLoc') {
          this.initMap();
        } else if (status == 'CurLoc') {
          this.getCurrentPosition();
        } else if (status == 'MyLoc') {
          this.setMyLocation();
        }
      },
      // Map operation
      initMap() {
        var myLatlng = this.currentpos;
        // var myLatlng = new google.maps.LatLng(-25.363882,131.044922);
        var mapOptions = {
          zoom: 18,
          center: myLatlng
        };
        var mapppppp = new google.maps.Map(document.getElementById("mapf"), mapOptions);
        // Place a draggable marker on the map
        var marker = new google.maps.Marker({
            position: myLatlng,
            map: mapppppp,
            draggable:true,
            title:"Drag me!"
        });
        this.marker = marker;
        this.map = mapppppp;
      },
      lockMap(ll) {
        this.supporter = true;
        // Use reverse geocoding to get address info
        var geocoder = new google.maps.Geocoder;
        var latlng = !ll? {lat:this.marker.position.lat(), lng:this.marker.position.lng()} : ll;
        this.marker.draggable = !this.marker.draggable;
        var self = this;
        geocoder.geocode({'location': latlng}, function(results, status) {
          if (status === 'OK') {
            if (results[0]) {
              console.log(results[0], results[0].formatted_address);
              self.init_msg.address = results[0].formatted_address;
              self.init_msg.lat = self.marker.position.lat();
              self.init_msg.lng = self.marker.position.lng();
            } else {
              console.log('No results found');
            }
          } else {
            console.log('Geocoder failed due to: ' + status);
          }
        });
      },
      getCurrentPosition () {
        this.supporter = false;
        var geocoder = new google.maps.Geocoder;
        var self = this;
        geocoder.geocode({'location': this.currentpos}, function(results, status) {
          if (status === 'OK' && results[0]) {
            self.currentaddress = results[0].formatted_address;
            self.init_msg.address = results[0].formatted_address;
            self.init_msg.lat = self.currentpos.lat;
            self.init_msg.lng = self.currentpos.lng;
            console.log("current:", self.currentaddress)
          } else {
            console.log("Cannot get current location yet!")
          }
        });
      },
      // Set my profile location to the init message
      setMyLocation() {
        console.log("Setting my default profile to message!")
        this.init_msg.msg_status = "MyLoc";
        this.init_msg.address = this.myposition.address;
        this.init_msg.lat = this.myposition.lat;
        this.init_msg.lng = this.myposition.lng;
        console.log("init_msg.address:", this.init_msg.address);
      },
      //Submit the whole form
      submitMessage() {
        var self = this;
        var ids = this.init_msg.members;
        var title = this.init_msg.replyto ? 'Reply to:'+this.init_msg.replyto.title : self.init_msg.topic;
        $.ajax({
          url: '/dbproj/public/broadcastMessage',
          data: {
            topic: title,
            text: self.init_msg.text,
            thread: self.thread.info.thdid,
            address: self.init_msg.address,
            lat: self.init_msg.lat,
            lng: self.init_msg.lng,
            replyto: self.init_msg.replyto?self.init_msg.replyto.mid:null,
          },
          type: "GET",
          dataType: "json",
        }); 
        var currentdate = new Date();
        var datetime = currentdate.getFullYear() + "-" + (currentdate.getMonth()+1)  + "-" + currentdate.getDate() + " " + currentdate.getHours() + ":"  + currentdate.getMinutes() + ":" + currentdate.getSeconds();
        this.thread.messages.push({ "uname": self.myname, "thdid":  self.thread.id, "author":  self.myid, "mtimestamp": datetime, "title": title, "text":  self.init_msg.text, "address": null, "replyto": self.init_msg.replyto?self.init_msg.replyto.mid:null })
        this.$message(this.is_init_msg?'Message and new thread have been submitted':'Messged has been delivered!');
        this.init_msg.replyto = null;
        if (this.dialogFormVisible) this.dialogFormVisible = false;
      },
      // Load thread message and show drawer:
      loadThreadMessages(thdid, title){
        var self = this;
        this.thread.title = title;
        this.thread.id = thdid;
        $.ajax({
            url: '/dbproj/public/getThreadMessages',
            type: "GET",
            dataType: "json",
            data: { thdid: thdid },
            success:function (response) {
              self.thread.members = response.members;
              self.thread.messages = response.messages;
              self.drawer = true;
            }
        });
      },
      // Open Window
      emptyInitMsg() {
        this.init_msg.text = '';
        this.init_msg.topic = '';
        this.init_msg.members = [];
        this.init_msg.isfriend = false;
        this.init_msg.msg_status = 'None';
        this.init_msg.address = '';
        this.init_msg.lat = 0;
        this.init_msg.lng = 0;
        this.init_msg.replyto = null;
      },
      initNewMessage(isreply) {
        this.is_init_msg = true;
        this.emptyInitMsg();
        this.dialogFormVisible = true;
      },
      blockNewMessage(isreply) {
        this.is_init_msg = false;
        this.emptyInitMsg();
        this.dialogFormVisible = true;
      },
      replyMessage(replyto) {
        this.is_init_msg = false;
        this.emptyInitMsg();
        console.log("this.thread.messages.filter(data => data.mid == replyto)", this.thread.messages.filter(data => data.mid == replyto))
        this.init_msg.replyto = this.thread.messages.filter(data => data.mid == replyto)[0];
        this.dialogFormVisible = true;
      },
      // load more messages from backend
      load() {
        console.log("need to load");
      }
    },
    mounted () {
      self = this;
      $.ajax({
          url: '/dbproj/public/getLocalMessages',
          data: { type: "broadcast"},
          type: "GET",
          dataType: "json",
          success:function (response) {
            self.thread.info = response.thread;
            self.thread.messages = response.blockmessages;
            self.block = response.blockinfo[0];
            console.log("beforecreate response", response);
          }
      });
      $.ajax({
          url: '/dbproj/public/getmyinfo',
          type: "GET",
          dataType: "json",
          data: { type: "profile" },
          success:function (response) {
            self.myid = response.profile.uid;
            self.myname = response.profile.uname;
            self.myposition = {
              address: response.profile.address,
              lat: response.profile.lat,
              lng: response.profile.lng
            }
          }
      });
      if (navigator.geolocation) {
        var self = this;
        navigator.geolocation.getCurrentPosition(function(position) {
          var pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };
          self.currentpos = pos;
        }, function() {
          console.log("cannot load")
        });
      } else {
        // Browser doesn't support Geolocation
        alert("Browser doesn't support!");
      }
    },
    
  } 
</script>

<style lang='scss' scoped>
.centerform {
  margin-left: 10%;
  width: 80%;
}
</style>