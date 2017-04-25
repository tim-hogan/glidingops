import React from 'react'
import {
  Table, TableBody, TableHeader, TableHeaderColumn, TableRow
} from 'material-ui/Table'

import Flight from './Flight'

import FlightsSample     from '../samples/FlightsSample'
import MembersSample     from '../samples/MembersSample'
import LaunchTypesSample from '../samples/LaunchTypesSample'
import AircraftsSample   from '../samples/AircraftsSample'
// import './DailyTimeSheet.css'

const DailyTimeSheet = () => {
  const appState = {
    flights:     FlightsSample.data,
    members:     MembersSample.data,
    launchTypes: LaunchTypesSample.data,
    aircrafts:   AircraftsSample.data
  }

  let rows = []
  appState.flights.forEach( flight => {
    const mappedFlight = Flight.mapStateToProps(flight.id, appState)
    rows.push(
      <Flight flight={ mappedFlight } key={ flight.seq }/>
    )
  })

  return (
    <div>
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
    </div>
  )
}

export default DailyTimeSheet