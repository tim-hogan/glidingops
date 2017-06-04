import React from 'react'
import PropTypes from 'prop-types'

import MainLayout  from '../layouts/MainLayout'
import FlightRow   from './FlightRow'
import FlightsList from './FlightsList'
import MainAppBar  from './MainAppBar'

const Flights = function({flights, onEditFlight}) {
  const navigationComponent = <MainAppBar title={ 'Daily time sheet' }/>
  return (
      <MainLayout navigationComponent={ navigationComponent }>
        <FlightsList flights={flights}
                     onEditFlight={onEditFlight}/>
      </MainLayout>
    )
}

Flights.propTypes ={
  flights: PropTypes.array.isRequired,
  onEditFlight: PropTypes.func.isRequired
}
export default Flights