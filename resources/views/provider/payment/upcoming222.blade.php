@extends('provider.layout.app')

@section('content')
<div class="pro-dashboard-head">
        <div class="container">
            <a href="{{url('provider/earnings')}}" class="pro-head-link">Payment Statements</a>
             <a href="{{url('provider/upcoming')}}" class="pro-head-link active">Upcoming</a>
		<a href="{{url('provider/upcoming222')}}" class="pro-head-link ">All Request</a>
			 
   <!--         <a href="new-provider-patner-invoices.html" class="pro-head-link">Payment Invoices</a>
            <a href="new-provider-banking.html" class="pro-head-link">Banking</a> -->
        </div>
    </div>

    <div class="pro-dashboard-content">
        
        <!-- Earning Content -->
        <div class="earning-content gray-bg">
            <div class="container">
 

                <!-- Earning section -->
                <div class="earning-section earn-main-sec pad20">
                    <!-- Earning section head -->
                    <div class="earning-section-head row no-margin">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-left-padding">
                            <h3 class="earning-section-tit">All @lang('main.service')s</h3>
                        </div>
                    </div>
                    <!-- End of earning section head -->

                    <!-- Earning-section content -->
                    <div class="tab-content list-content">
                        <div class="list-view pad30 ">
                            
                            <table class="earning-table table table-responsive">
                                <thead>
                                    <tr>
                                        <th>Pickup Time</th>
                                        <th>Service</th>
                                        <th>Pickup Address</th>
                                        <th>Status</th><th>User</th>
										<th>Time</th>
                                        <th>Timer</th>
										<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php //echo "<pre>";print_r($data); ?>
                                @foreach($data as $each)
                                    <tr>
                                        <td>{{date('Y D, M d - H:i A',strtotime($each->schedule_at))}}
										</td>
                                        
										  <td>{{$each->service_name}}</td>
										<td>{{$each->s_address}}</td>
										<td>{{$each->status}}</td>
										<td>{{$each->first_name}}-{{$each->last_name}}</td>
										<td>{{$each->started_at}}</td>
										<td style="width: 86px;">
										    <span id="timerM_<?php echo $each->id?>"></span>:
										    <span id='timerS__<?php echo $each->id?>'>0</span>
											
										</td>
									
			 	
								
										
							<div class="row">

						<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
						       	<script>
                                $(document).ready(function()
								{
								 var st = '<?php echo $each->status;?>';
								 if(st == 'STARTED')
								 {
									 setInterval(function se_<?php echo $each->id?>()
									 {
										 //alert("Hello"); 
var today = new Date();
var old_date = '<?php echo $each->started_at;?>';
//var lastsecDATA = old_date.slice(-2);
//var lastsecNOW = today.slice(-2);
//alert(lastsecDATA);alert(lastsecNOW);
//var sec_diff = lastsecDATA-lastsecNOW;
var Christmas = new Date(old_date);
var diffMs = (today -Christmas); // milliseconds between now & Christmas
var diffDays = Math.floor(diffMs / 86400000); // days
var diffHrs = Math.floor((diffMs % 86400000) / 3600000); // hours
var diffMins = Math.round(((diffMs % 86400000) % 3600000) / 60000); // minutes
var diffSec = Math.round((((diffMs % 86400000) % 3600000) / 60000)/60000); // second
  var set_to_time =diffMins+':'+diffSec;
										document.getElementById("timerM_<?php echo $each->id?>").innerHTML = diffMins; 
										 }, 1000);
								 to_start_<?php echo $each->id?>();
								 
								 
								 }
								})
								</script>
								
						
<!--input type="button" name="btn" id='btn' value="Start" onclick="to_start_<?php echo $each->id;?>()";>
<br><br-->
<!--div id='n1_<?php echo $each->id;?>' value="" style="z-index: 2; position: relative; right: 0px; top: 10px; background-color: #00cc33;
 width: 100px; padding: 10px; color: white; font-size:20px; border: #0000cc 2px dashed; "> </div-->

<p id="result"></p>
<script language=javascript>

function to_start_<?php echo $each->id;?>()
{ //alert('ok');
	tm=window.setInterval('disp_'+<?php echo $each->id;?>+'()',1000);
	document.getElementById('btn').value='Stop';
}
function check()
{ 
  var node = document.getElementById('n1_'+<?php echo $each->id;?>);
  var text  = node.textContent || node.innerText;
  alert(text);
  var strArray = text.split(":");
  window.hh = strArray[0];
  window.mm = strArray[1];
  window.ss = strArray[2]; 
  //alert(hh);alert(mm);
  alert(ss);  
}



var h_<?php echo $each->id;?>=0;
var m_<?php echo $each->id;?>=0;
var s_<?php echo $each->id;?>=0;
function disp_<?php echo $each->id;?>(){
// Format the output by adding 0 if it is single digit //

if(s_<?php echo $each->id;?><10){var s1='0' + s_<?php echo $each->id;?>;}
else{var s1=s_<?php echo $each->id;?>;}
if(m_<?php echo $each->id;?><10){var m1='0' + m_<?php echo $each->id;?>;}
else{var m1=m_<?php echo $each->id;?>;}
if(h_<?php echo $each->id;?><10){var h1='0' + h_<?php echo $each->id;?>;}
else{var h1=h_<?php echo $each->id;?>;}
// Display the output //
str_<?php echo $each->id;?>= h1 + ':' + m1 +':' + s1 ;
strSEC_<?php echo $each->id;?> = s1;
document.getElementById('timerS__'+<?php echo $each->id;?>).innerHTML=strSEC_<?php echo $each->id;?>;
//document.getElementById('n1_'+<?php echo $each->id;?>).innerHTML=str_<?php echo $each->id;?>;

if (typeof(Storage) !== "undefined") {
  // Store
  sessionStorage.setItem("timer_"+<?php echo $each->id;?>, str_<?php echo $each->id;?>);
  // Retrieve
//  document.getElementById('n1_'+<?php echo $each->id;?>).innerHTML = sessionStorage.getItem("timer_"+<?php echo $each->id;?>);
}

// Calculate the stop watch // 
if(s_<?php echo $each->id;?><59){ 
s_<?php echo $each->id;?>=s_<?php echo $each->id;?>+1;
}else{
s_<?php echo $each->id;?>=0;
m_<?php echo $each->id;?>=m_<?php echo $each->id;?>+1;
if(m_<?php echo $each->id;?>==60){
m_<?php echo $each->id;?>=0;
h_<?php echo $each->id;?>=h_<?php echo $each->id;?>+1;
} // end if  m ==60
}// end if else s < 59
// end of calculation for next display

}
</script>
</div>
										
<td>
<!--form action="{{url('update_service_status')}}" method="POST">
	{{ csrf_field() }}
	<input type="hidden" name="id" value="{{$each->id}}">

<input type="hidden" name="status" value="STARTED">
<button class="full-primary-btn fare-btn" onclick="check()">TO STARTED</button>
</form-->



	<form action="{{url('update_service_status')}}" method="POST" enctype ="multipart/form-data">
	{{ csrf_field() }}
	<input type="hidden" name="id" value="{{$each->id}}">
<?php if($each->status== 'STARTED'){?>
	<input type="hidden" name="status" value="DROPPED">
	<label>Before Image </label>
	<input type="file" accept="image/*" capture name="bef_image" class="form-controls">
	<input type="hidden" name="tag" value="before">
	<button class="full-primary-btn fare-btn" onclick="return confirm('Are you sure?')">End Service</button>
<?php }

else if($each->status== 'DROPPED'){ ?>
	<input type="hidden" name="status" value="COMPLETED">
	<label>After Image </label>
	<input type="file" accept="image/*" capture name="bef_image" class="form-controls">
	<input type="hidden" name="tag" value="after">
	<button class="full-primary-btn fare-btn" onclick="return confirm('Are you sure?')">PAYMENT</button>
<?PHP } ?>
	  
	</form>
</td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
<!-- CODE FOR TIMER -->							
<div class="row">
<style>
* {
  margin: 0;
  padding: 0;
}

html {
  background: #333;
  color: #bbb;
  font-family: Menlo;
}

.controls {
  position: fixed;
  text-align: center;
  top: 1em;
  width: 100%;
}

.button {
  color: #bbb;
  font-size: 4vw;
  margin: 0 0.5em;
  text-decoration: none;
}

.button:first-child {
    margin-left: 0;
}

.button:last-child {
    margin-right: 0;
}

.button:hover {
  color: white;
}

.stopwatch {
  font-size: 14px;
  height: 100%;
  text-align: center;
}

.results {
  border-color: lime;
  list-style: none;
  margin: 0;
  padding: 0;
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
}
</style>
	
	
	
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script>
	class Stopwatch {
    constructor(display, results) {
        this.running = false;
        this.display = display;
        this.results = results;
        this.laps = [];
        this.reset();
        this.print(this.times);
    }
    
    reset() {
        this.times = [ 0, 0, 0 ];
    }
    
    start() {
        if (!this.time) this.time = performance.now();
        if (!this.running) {
            this.running = true;
            requestAnimationFrame(this.step.bind(this));
        }
    }
    
    lap() {
        let times = this.times;
        let li = document.createElement('li');
        li.innerText = this.format(times);
        this.results.appendChild(li);
    }
    
    stop() {
        this.running = false;
        this.time = null;
    }

    restart() {
        if (!this.time) this.time = performance.now();
        if (!this.running) {
            this.running = true;
            requestAnimationFrame(this.step.bind(this));
        }
        this.reset();
    }
    
    clear() {
        clearChildren(this.results);
    }
    
    step(timestamp) {
        if (!this.running) return;
        this.calculate(timestamp);
        this.time = timestamp;
        this.print();
        requestAnimationFrame(this.step.bind(this));
    }
    
    calculate(timestamp) {
        var diff = timestamp - this.time;
        // Hundredths of a second are 100 ms
        this.times[2] += diff / 10;
        // Seconds are 100 hundredths of a second
        if (this.times[2] >= 100) {
            this.times[1] += 1;
            this.times[2] -= 100;
        }
        // Minutes are 60 seconds
        if (this.times[1] >= 60) {
            this.times[0] += 1;
            this.times[1] -= 60;
        }
    }
    
    print() {
        this.display.innerText = this.format(this.times);
    }
    
    format(times) {
        return `\
${pad0(times[0], 2)}:\
${pad0(times[1], 2)}:\
${pad0(Math.floor(times[2]), 2)}`;
    }
}

function pad0(value, count) {
    var result = value.toString();
    for (; result.length < count; --count)
        result = '0' + result;
    return result;
}

function clearChildren(node) {
    while (node.lastChild)
        node.removeChild(node.lastChild);
}

let stopwatch = new Stopwatch(
    document.querySelector('.stopwatch'),
    document.querySelector('.results'));
	</script>
</div>
                        </div>

                    </div>
                <!-- End of earning section -->
            </div>
        </div>
        <!-- Endd of earning content -->
    </div>                
</div>
@endsection

@section('scripts')
<script type="text/javascript">
   
</script>
@endsection