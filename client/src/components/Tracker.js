import React, { Component } from 'react'

import MainAppBar from './MainAppBar'
import MainLayout from '../layouts/MainLayout'

class Tracker extends Component {

  render() {
    return (
      <MainLayout
        navigationComponent={
          <MainAppBar title={ 'Daily time sheet' } />
        }
      >
        <div>Tracker</div>
      </MainLayout>
    )
  }
}

export default Tracker