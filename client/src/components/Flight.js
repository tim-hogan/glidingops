import React, { Component } from 'react'
import PropTypes from 'prop-types'

class Flight extends Component {
  static propTypes = {
    flight: PropTypes.object.require
  }

  render() {
    return (
      <div className='row'>
        <div className='col-sm-10'>
          <div className='row'>
            <div className='col-sm-2'>
              This is the long column
            </div>
            <div className='col-sm-2'>
              This is the long column
            </div>
            <div className='col-sm-2'>
              This is the long column
            </div>
            <div className='col-sm-2'>
              This is the long column
            </div>
            <div className='col-sm-2'>
              This is the long column
            </div>
            <div className='col-sm-2'>
              This is the long column
            </div>
          </div>
        </div>
        <div className='col-sm-2'>
          This is the short column
        </div>
      </div>
    )
  }
}

export default Flight