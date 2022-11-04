require('dotenv').config()
import React, { Component } from 'react';
import ReactDOM from 'react-dom';
const axios = require('axios');

export default class Index extends Component {
    constructor(props) {
        super(props);
        this.state = { visible: true, airportICAO: 'CYWG', airportName: 'Winnipeg / James Armstrong Richardson International Airport', flightRules: 'N/A', metar: 'N/A' };
    }

    componentDidMount() {
        let final_weather = [];
        function prettyWeather(x) {
            let array = [];
            switch (x['icao']) {
                case "CYWG":
                    final_weather[0] = x;
                    break;
                case "CYXE":
                    final_weather[1] = x;
                    break;
                case "CYQT":
                    final_weather[2] = x;
                    break;
                case "CYQR":
                    final_weather[3] = x;
                    break;
                case "CYMJ":
                    final_weather[4] = x;
                    break;
                case "CYPG":
                    final_weather[5] = x;
                    break;
            }
        }

        const url = 'https://api.checkwx.com/metar/CYWG,CYXE,CYQR,CYQT,CYPG,CYMJ/decoded';
        const options = {
            headers: {'X-API-Key': process.env.MIX_REACT_APP_AIRPORT_API_KEY}
        };

        axios.get(url, options).then(resp => {
            const weather = resp.data['data'];
            weather.forEach(prettyWeather);
        });
    }

    render() {
        return (
            <div className="card card-background" style={{width: '100%'}}>
                <div className="card-header" style={{color: '#122b44'}}>
                    <h2 className="font-weight-bold" style={{textAlign: 'center'}}><i className="fas fa-sun"></i>&nbsp;&nbsp;Weather</h2>
                </div>
                <div className="card-body">
                    <div className={this.state.visible?'fadeIn':'fadeOut'} style={{float: 'left'}}>
                        <h5 className="align-middle font-weight-bold">
                            {this.state.airportICAO} - {this.state.airportName}&nbsp;&nbsp;
                            <span className={'badge ' + this.state.flightRules}>{this.state.flightRules}</span>
                        </h5>
                        <p>{this.state.metar}</p>
                    </div>
                </div>
                <div className="card-footer"></div>
            </div>
        )
    }
}

ReactDOM.render(<Index />, document.getElementById('index-weather'));
