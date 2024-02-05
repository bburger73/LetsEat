import React, { useEffect }  from 'react';
import { useFocusEffect } from '@react-navigation/native';
import { ActivityIndicator, KeyboardAvoidingView, BackHandler, SafeAreaView, StyleSheet, Text, View, TextInput, TouchableOpacity, ScrollView, Platform } from "react-native";
import { useToast } from "react-native-toast-notifications";
import Button from './button';
import './global.js';

export default function UpdateFeastGroup(props) {
    useFocusEffect(
        React.useCallback(() => {
            const onBackPress = () => {
                goback();
                return true;
            };

            BackHandler.addEventListener(
                'hardwareBackPress', onBackPress
            );

            return () =>
                BackHandler.removeEventListener(
                    'hardwareBackPress', onBackPress
                );
        }, [])
    );

    useEffect(() => {
        const requestOptions = {
            method: "GET",
            headers: {
                "LETSEAT-AUTHKEY": props.user.user_token,
                "Content-Type": "application/json"
            },
        };
          
        fetch(global.server + "user_account/read.php?all=1",requestOptions).then(res => res.json()).then(result => {
            setFeasts(result);
            return result
        }).then(Feastsx=>{
            fetch(global.server + "feast_user_link/read.php?feast_group_id=" + props.feast,requestOptions).then(res => res.json()).then(result => {
                let hold = Feastsx.map(rest => result.includes(rest.user_id)?{...rest,selected:true}:{...rest,selected:false});
                setGroupFeasts(hold)
            });
        })
          
    },[]);

    const styles = StyleSheet.create(
        Platform.select({
            ios: global.mainstyles,
            android: global.mainstyles,
            default: global.mainstyles
        })
    );


    const [Feasts,setFeasts] = React.useState([]);
    const [FeastGroups,setGroupFeasts] = React.useState([]);





    const [name, setName] = React.useState("");
    const [notes, setNotes] = React.useState("");
    const toast = useToast();
    const [isLoadingName, setLoadingName] = React.useState(false);

    const notesRef = React.useRef(null);


    const confirmUpdateGroup = () => {
        setLoadingName(true);
        // if (name !== '' && name !== null && name !== undefined) {
            const requestOptions = {
                method: "PUT",
                headers: {
                    "LETSEAT-AUTHKEY": props.user.user_token,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    id:props.feast,
                    feasts:FeastGroups
                }),
            };
            console.log(requestOptions.body)

            fetch(global.server + "feast_user_link/update.php", requestOptions).then(res => res.json()).then(result => {
                // console.log(result);
                setLoadingName(false);
                if (result.result) {
                    alert("Updated Successfully!");
                } else {
                    alert("Not Updated");
                }
            }).catch(() => {
                setTimeout(() => {
                    setLoadingName(false);
                    alert("Failed To Connect To Server");
                }, 2000);
            }).finally(() => {
                setTimeout(() => setLoadingName(false), 2000);
            });
        // } else {
        //     toast.show("Please enter text into the field", {
        //         type: "warning",
        //         placement: "top",
        //         duration: 4000,
        //         offset: 100,
        //         animationType: "slide-in",
        //     });
        //     setLoadingName(false);
        // }
    }
    const goback = () => {
        props.pagging(12)
    }
    // const goToAdd = () => {
    //     props.pagging(6)
    // }
    const updateSelect = (id) => {
        setGroupFeasts(FeastGroups.map(Feast => Feast.user_id===id?{...Feast,selected:!Feast.selected}:Feast));
    }

    return (
        <SafeAreaView>
            <View style={styles.main}>
                <View style={styles.navbar}>
                    <View style={styles.backbuttonview}>
                        <Button
                            textstyle={styles.backbutton}
                            title="Back"
                            onPress={goback}>
                        </Button>
                    </View>
                    {/* <View style={styles.addbuttonview}>
                        <Button
                            textstyle={styles.addbutton}
                            title="Add"
                            onPress={goToAdd}>
                        </Button>
                    </View> */}
                </View>
                <View style={styles.scrollView}>
                    <KeyboardAvoidingView
                        behavior={Platform.OS === "ios" ? "height" : "padding"}
                        enabled>
                        <ScrollView>
                            <View
                                style={styles.signin}
                            >
                                
                                <Text style={styles.dashboardheader}>
                                    Update Feast Group
                                </Text>
                                {/* <Text>Group Name</Text>
                                <TextInput
                                    style={styles.input}
                                    onChangeText={setName}
                                    value={name}
                                    placeholder="Doe Family Dinner"
                                /> */}
                                <Text>Feasts</Text>
                                {
                                    FeastGroups.map(group => {
                                        return(
                                        <TouchableOpacity
                                            key={Math.random(50000)}
                                            id={true}
                                            name="notification"
                                            onPress={() => updateSelect(group.user_id)}
                                        >
                                            <View
                                            key={Math.random(50000)} style={{
                                                flexDirection: 'row'
                                            }}>
                                                <View
                                            key={Math.random(50000)} style={{
                                                    margin: 5,
                                                    height: 24,
                                                    width: 24,
                                                    borderRadius: 12,
                                                    borderWidth: 2,
                                                    borderColor: '#000',
                                                    alignItems: 'center',
                                                    justifyContent: 'center',
                                                }}>
                                                    {
                                                        group.selected ?
                                                            <View
                                                            key={Math.random(50000)} style={{
                                                                height: 12,
                                                                width: 12,
                                                                borderRadius: 6,
                                                                backgroundColor: '#000',
                                                            }} />
                                                            : null
                                                    }
                                                </View>
                                                <Text
                                            key={Math.random(50000)} style={{
                                                    color: "black",
                                                    fontSize: 20,
                                                    margin: 5
                                                }}
                                                >{group.email}</Text>
                                            </View>
                                        </TouchableOpacity>)
                                    })
                                }
                                <Button
                                    onPress={() => {
                                        confirmUpdateGroup();
                                    }}
                                    title="Update Group"
                                    // textstyle={styles.button}
                                    textstyle={styles.topbutton}
                                    accessibilityLabel="Update Group"
                                    isLoading={isLoadingName}
                                />
                            </View>
                        </ScrollView>
                    </KeyboardAvoidingView>
                </View>
            </View>
        </SafeAreaView>

    )
}