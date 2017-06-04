import React from 'react'
import PropTypes from 'prop-types'

import FlightRow  from './FlightRow'

const FlightsList = ({flights, onEditFlight}) => {
  let rows = []
    flights.forEach( flight => {
      rows.push(
        <FlightRow flight={ flight } key={ flight.seq } onEdit={onEditFlight}/>
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

FlightsList.propTypes ={
  flights: PropTypes.array.isRequired,
  onEditFlight: PropTypes.func.isRequired
}

export default FlightsList