import React, { Component } from 'react'
import {
  Table, TableBody, TableHeader, TableHeaderColumn, TableRow
} from 'material-ui/Table'

import FlightRow from './FlightRow'
import FlightEdit from './FlightEdit'

import FlightsSample     from '../samples/FlightsSample'
import MembersSample     from '../samples/MembersSample'
import LaunchTypesSample from '../samples/LaunchTypesSample'
import AircraftsSample   from '../samples/AircraftsSample'
// import './DailyTimeSheet.css'

class DailyTimeSheet extends Component {

  constructor(props) {
    super(props);
    this.state = {editing: null};
  }

  renderEditFlight = (flight)  => {
    return (
      <FlightEdit flight={flight}/>
    )
  }

  editFlight = (flight) => {
    this.setState({editing: flight})
  }

  renderFlights = () => {
    const appState = {
      flights:     FlightsSample.data,
      members:     MembersSample.data,
      launchTypes: LaunchTypesSample.data,
      aircrafts:   AircraftsSample.data
    }

    let rows = []
    appState.flights.forEach( flight => {
      const mappedFlight = FlightRow.mapStateToProps(flight.id, appState)
      rows.push(
        <FlightRow flight={ mappedFlight } key={ flight.seq } onEdit={this.editFlight}/>
      )
    })

    return (
      <table className='DailyTimeSheet-flights'>
        <thead style={ {
          borderBottomColor: 'rgb(224, 224, 224)',
          borderBottomStyle: 'solid',
          borderBottomWidth: '1px'
        } }>
          <tr>
            <th></th>
            <th>SEQ</th>
            <th>LAUNCH</th>
            <th>GLIDER</th>
            <th>TOW PILOT<br/>WINCH DRIVER</th>
            <th>PIC</th>
            <th>P2</th>
            <th>START</th>
            <th>LAND</th>
            <th>HEIGHT</th>
            <th>TIME</th>
            <th>BILLING</th>
            <th>COMMENTS</th>
            <th>DELETED</th>
          </tr>
        </thead>
        <tbody>
          { rows }
        </tbody>
      </table>
    )
  }

  render() {
    if(this.state.editing) {
      return this.renderEditFlight(this.state.editing)
    } else {
      return this.renderFlights()
    }
  }
}

export default DailyTimeSheet