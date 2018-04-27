export function Util(){
	let _f = {};

	var lock = false;


	_f.formatDate = function(time){
	  let t = new Date(time);
	  // console.log(t.getHours())
	  let [y, m , d, h, s, tt] = [t.getFullYear(), (t.getMonth() + 1), t.getDate(), t.getHours(), t.getMinutes(), ""];

	  if(m.toString().length == 1){
	    m = "0" + m;
	  }

	  if(d.toString().length == 1){
	    d = "0" + d;
	  }

	  if(h.toString().length == 1){
	    h = "0" + h;
	  }

	  if(s.toString().length == 1){
	    s = "0" + s;
	  }

		tt = y + "-" + m + "-" + d + " " + h + ":" + s;
	  return tt;
	}

	_f.formatDate2 = function(time){
	  let t = new Date(time);
	  // console.log(t.getHours())
	  let [y, m , d, h, s, tt] = [t.getFullYear(), (t.getMonth() + 1), t.getDate(), t.getHours(), t.getMinutes(), ""];

	  if(m.toString().length == 1){
	    m = "0" + m;
	  }

	  if(d.toString().length == 1){
	    d = "0" + d;
	  }

	  if(h.toString().length == 1){
	    h = "0" + h;
	  }

	  if(s.toString().length == 1){
	    s = "0" + s;
	  }

	  tt = y + "-" + m + "-" + d;
	  return tt;
	}


	return _f;
}