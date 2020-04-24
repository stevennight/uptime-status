import React, { useEffect, useState } from 'react';
import UptimeItem from './uptime-item';
import { GetMonitors } from '../utils/uptimerobot';

const Uptime = (props) => {

  const { CountDays } = window.Config;
  const { apikey } = props;
  const [ monitors, setMonitors ] = useState(null);

  useEffect(() => {
    GetMonitors(apikey, CountDays).then(setMonitors, setMonitors);
  }, [apikey]);

  let res;
  if(!monitors){
    res = (<div className="item loading"></div>)
  } else if(monitors.stat === 'fail'){
    res = (<div className="item">{monitors.error.message}</div>)
  } else {
    res = monitors.map(item => (
      <UptimeItem key={item.id} monitor={item} />
    ))
  }
  return res;
  // return monitors ? monitors.map(item => (
  //   <UptimeItem key={item.id} monitor={item} />
  // )) : (
  //   <div className="item loading"></div>
  // );
}

export default Uptime;
