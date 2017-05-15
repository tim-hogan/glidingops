import React, { Component } from 'react'

import FlightRow  from './FlightRow'
import FlightEdit from './FlightEdit'
import MainAppBar from './MainAppBar'
import EditAppBar from './EditAppBar'
import MainLayout from '../layouts/MainLayout'

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

  doneEditing = () => {
    this.setState({editing: null})
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
    const slots = (this.state.editing) ?
    {
      navigationComponent: <EditAppBar title={ `Edit flight ${this.state.editing.seq}` }
                                      doneHandler={this.doneEditing}/>,
      content: this.renderEditFlight(this.state.editing)
    } :
    {
      navigationComponent: <MainAppBar title={ 'Daily time sheet' }/>,
      content: this.renderFlights()
    }

    return (
      <MainLayout navigationComponent={ slots.navigationComponent }>
        { slots.content }
      </MainLayout>
    )
  }
}

export default DailyTimeSheet