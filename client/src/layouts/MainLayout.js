import React, { Component } from 'react'
import PropTypes from 'prop-types'

class MainLayout extends Component {
  static propTypes = {
    navigationComponent: PropTypes.node
  }

  render() {
    return (
      <div>
      <div className='row'>
        <div className='col-xs-12'>
          { this.props.navigationComponent }
        </div>
      </div>
      <div className='row'>
        <div className='col-xs-12'>
          { this.props.children }
        </div>
      </div>
      </div>
    )
  }
}

export default MainLayout