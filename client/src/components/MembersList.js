import React, { Component } from 'react'
import PropTypes from 'prop-types'

import AppBar from 'material-ui/AppBar'
import Drawer from 'material-ui/Drawer'
import IconButton from 'material-ui/IconButton'
import { List, ListItem } from 'material-ui/List'

import NavigationClose from 'material-ui/svg-icons/navigation/close'

class MembersList extends Component {
  static propTypes = {
    open: PropTypes.bool,
    members: PropTypes.arrayOf(PropTypes.object),
    searchTermMinLength: PropTypes.number,
    onSelect: PropTypes.func,
    onRequestClose: PropTypes.func
  }

  static defaultProps = {
    searchTermMinLength: 1
  }

  constructor(props) {
    super(props)
    this.state = {
      searchTerm: '',
      filteredMembers: []
    }
  }

  onSearchChange = (event) => {
    let value = event.target.value
    if(value.trim().length === 0) {
      value = ''
    }

    let filteredMembers = []
    if(value.length >= this.props.searchTermMinLength) {
      filteredMembers = this.props.members.filter((member) => {
        return member.displayname.toUpperCase().startsWith(value.toUpperCase())
      })
    }

    this.setState({
      searchTerm: value,
      filteredMembers: filteredMembers
    })
  }

  clear = () => {
    this.setState({
      searchTerm: '',
      filteredMembers: []
    })
  }

  listItemSelectedHandler = (member) => {
    this.clear()
    this.props.onSelect(member)
  }

  renderFilteredList = () => {
    if(this.state.searchTerm.length < this.props.searchTermMinLength) {
      return (
        <p style={{
          opacity: '1',
          color: 'rgba(0, 0, 0, 0.298039)',
          paddingLeft: '14px',
          paddingRight: '14px',
          fontSize: '16px',
          lineHeight: '24px'
        }}>
          Please enter at least {this.props.searchTermMinLength} characters in the search box
        </p>
      )
    }

    let memberItems = []
    this.state.filteredMembers.forEach((member) => {
      memberItems.push(
        <ListItem key={member.id} primaryText={member.displayname}
          onTouchTap={() => {this.listItemSelectedHandler(member)}}/>
      )
    })

    const listStyle = {
      position: 'absolute',
      top: '0px',
      bottom: '0px',
      marginTop: '72px',
      overflowX: 'scroll',
      width: '100%'
    }

    return (
      <List style={listStyle}>
        {memberItems}
      </List>
    )
  }

  closeButtonHandler = () => {
    this.clear
    this.props.onRequestClose()
  }

  renderCloseButton = () => {
    return (
      <IconButton>
        <NavigationClose onTouchTap={this.closeButtonHandler}/>
      </IconButton>
    )
  }

  render() {
    return (
      <Drawer open={this.props.open} openSecondary={true} docked={false}>
        <AppBar iconElementLeft={this.renderCloseButton()}>
          <div style={{marginTop: 'auto', marginBottom: 'auto'}}>
            <input type='text' value={this.state.searchTerm} onChange={this.onSearchChange}/>
          </div>
        </AppBar>
        {this.renderFilteredList()}
      </Drawer>
    )
  }
}

export default MembersList