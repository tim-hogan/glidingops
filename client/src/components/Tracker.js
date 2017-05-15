import React, { Component } from 'react'

import MainAppBar from './MainAppBar'
import MainLayout from '../layouts/MainLayout'

class Tracker extends Component {

  render() {
    return (
      <MainLayout
        navigationComponent={
          <MainAppBar title={ 'Flights live tracker' } />
        }
      >
        <div>Tracker</div>
      </MainLayout>
    )
  }
}

export default Tracker