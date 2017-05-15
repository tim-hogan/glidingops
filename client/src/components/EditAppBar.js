import React, { Component } from 'react'
import PropTypes from 'prop-types'

import AppBar from 'material-ui/AppBar'
import IconButton from 'material-ui/IconButton'

import NavigationBack from 'material-ui/svg-icons/navigation/arrow-back'

class EditAppBar extends Component {
  static propTypes = {
    title: PropTypes.string,
    doneHandler: PropTypes.func
  }

  render() {
    const title = (<div>{ this.props.title }</div>)

    return (
      <div>
        <AppBar
          title={ title }
          iconElementLeft={
            <IconButton onTouchTap={this.props.doneHandler} touch={true}>
              <NavigationBack/>
            </IconButton>
          }/>
      </div>
    )
  }
}

export default EditAppBar